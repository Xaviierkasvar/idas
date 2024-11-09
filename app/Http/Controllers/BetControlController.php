<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BetControl;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class BetControlController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        // Obtener los datos del día filtrados por rol
        $betControls = $this->getTodayBetControls($user, $today);

        // Calcular el monto total de apuestas del día
        $totalBetAmount = $betControls->sum('total_bet_amount');
        $totalSellerMargin = $betControls->sum('seller_margin');
        $totalHouseMargin = $betControls->sum('house_margin');

        // Obtener los usuarios filtrados por rol
        $users = $this->getFilteredUsers($user);

        return view('bets.betcontrol', compact('betControls', 'totalBetAmount', 'totalSellerMargin', 'totalHouseMargin', 'users', 'today'));
    }

    public function filter(Request $request)
    {
        $user = Auth::user();

        // Obtener controles de apuestas filtrados basados en el rol del usuario y parámetros de la solicitud
        $betControls = $this->getFilteredBetControls($request, $user);

        // Calcular el monto total de apuestas y márgenes desde los resultados
        $totalBetAmount = $betControls->sum('total_bet_amount');
        $totalSellerMargin = $betControls->sum('seller_margin');
        $totalHouseMargin = $betControls->sum('house_margin');

        // Obtener usuarios filtrados basados en el rol del usuario
        $users = $this->getFilteredUsers($user);

        return view('bets.betcontrol', compact('betControls', 'totalBetAmount', 'totalSellerMargin', 'totalHouseMargin', 'users'));
    }

    private function getTodayBetControls($user, $date)
    {
        $query = BetControl::whereDate('created_at', $date);

        // Aplicar filtro basado en el rol del usuario
        $this->applyBetControlRoleFilter($query, $user);

        // Cargar relaciones necesarias y paginar resultados
        return $query->with('user', 'associatedUser')->paginate(10);
    }

    private function getFilteredBetControls(Request $request, $user)
    {
        $query = BetControl::query();

        // Aplicar filtros de fecha si se proporcionan
        if ($request->start_date && $request->end_date) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        }

        // Aplicar filtros para usuario si se proporcionan
        if ($request->user_id) {
            $query->where(function ($query) use ($request) {
                // Primero, filtra por el user_id especificado
                $query->where('user_id', $request->user_id)
                      // Luego, filtra por los usuarios asociados a ese user_id
                      ->orWhereHas('user', function ($q) use ($request) {
                          $q->where('associated_id', $request->user_id);
                      })
                      // Y también puedes agregar otro nivel de asociaciones si es necesario
                      ->orWhereHas('user.associated', function ($q) use ($request) {
                          $q->where('associated_id', $request->user_id);
                      });
            });
        }
        // Aplicar filtros basados en el rol del usuario
        $this->applyBetControlRoleFilter($query, $user);
        // Cargar relaciones necesarias y paginar resultados
        return $query->with('user', 'associatedUser')->paginate(10);
    }

    private function applyBetControlRoleFilter($query, $user)
    {
        switch ($user->role_id) {
            case 4:
                $query->where('user_id', $user->id);
                break;

            case 3:
                $query->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->orWhereHas('user', function ($q) use ($user) {
                              $q->where('associated_id', $user->id);
                          });
                });
                break;

                case 2:
                    $query->where(function ($query) use ($user) {
                        // Primera parte: El usuario directamente asociado
                        $query->where('user_id', $user->id)
                              ->orWhereHas('user', function ($q) use ($user) {
                                  $q->where('associated_id', $user->id);
                              })
                              // Segunda parte: Usuarios asociados a los usuarios del primer nivel
                              ->orWhereHas('user.associated', function ($q) use ($user) {
                                  $q->where('associated_id', $user->id);
                              })
                              // Tercera parte: Usuarios asociados a los usuarios asociados (el segundo nivel de asociación)
                              ->orWhereHas('user.associated.associated', function ($q) use ($user) {
                                  $q->where('associated_id', $user->id);
                              });
                    });
                    break;

            case 1:
                // Sin restricciones, mostrar todos los resultados para el rol 1
                break;
        }
    }

    private function getFilteredUsers($user)
    {
        $usersQuery = User::query();

        // Aplicar filtros basados en el rol del usuario
        $this->applyUserRoleFilter($usersQuery, $user);

        return $usersQuery->get();
    }

    private function applyUserRoleFilter($query, $user)
    {
        switch ($user->role_id) {
            case 4:
                $query->where('id', $user->id);
                break;

            case 3:
                $query->where('id', $user->id)
                      ->orWhere(function ($query) use ($user) {
                          $query->where('associated_id', $user->id)
                                ->where('role_id', 4);
                      });
                break;

            case 2:
                $query->where('id', $user->id)
                        ->orWhere(function ($query) use ($user) {
                            $query->where('associated_id', $user->id)
                                ->whereIn('role_id', [3, 4]);
                        })
                        ->orWhere(function ($query) use ($user) {
                            $query->whereIn('associated_id', function ($subQuery) use ($user) {
                                $subQuery->select('id')
                                        ->from('users')
                                        ->where('associated_id', $user->id)
                                        ->whereIn('role_id', [3, 4]);
                            })->where('role_id', 4);
                        });
                break;

            case 1:
                // Sin restricciones, mostrar todos los usuarios para el rol 1
                break;
        }
    }
}
