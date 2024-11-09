<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        // Get today's bets filtered by role
        $bets = $this->getTodayBets($user, $today);

        // Calculate the total bet amount from today's bets
        $totalBetAmount = $bets->sum('bet_amount');

        // Get users filtered by role
        $users = $this->getFilteredUsers($user);

        return view('reports.index', compact('bets', 'totalBetAmount', 'users'));
    }

    private function getTodayBets($user, $date)
    {
        $query = Bet::whereDate('created_at', $date);

        // Apply role-based filtering for bets
        $this->applyBetRoleFilter($query, $user);

        // Load necessary relations and paginate results
        return $query->with('user', 'associated')->paginate(10);
    }

        public function filter(Request $request)
    {
        $user = Auth::user();

        // Get filtered bets based on user role and request parameters
        $bets = $this->getFilteredBets($request, $user);
        
        // Calculate the total bet amount from the results
        $totalBetAmount = $bets->sum('bet_amount');

        // Get filtered users based on user role
        $users = $this->getFilteredUsers($user);

        return view('reports.index', compact('bets', 'totalBetAmount', 'users'));
    }

    private function getFilteredBets(Request $request, $user)
    {
        $query = Bet::query();

        // Apply date filters if provided
        if ($request->start_date && $request->end_date) {
            $query->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date);
        }

        // Apply filters for specific bet number and user ID if provided
        if ($request->bet_number) {
            $query->where('bet_number', $request->bet_number);
        }
        
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
        // Apply role-based filtering for bets
        $this->applyBetRoleFilter($query, $user);
        // Load necessary relations and paginate results
        return $query->with('user', 'associated')->paginate(10);
    }

    private function applyBetRoleFilter($query, $user)
    {
        switch ($user->role_id) {
            case 4:
                // Solo ve sus propios datos
                $query->where('user_id', $user->id);
                break;

            case 3:
                // Ve sus propios datos y los datos de usuarios con rol 4 asociados a él
                $query->where('user_id', $user->id)
                    ->orWhereHas('user', function ($q) use ($user) {
                        $q->where('associated_id', $user->id)
                            ->where('role_id', 4);
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
                // Sin restricciones, ve todos los resultados para rol 1
                break;
        }
    }

    private function getFilteredUsers($user)
    {
        $usersQuery = User::query();

        // Apply role-based filtering for users
        $this->applyUserRoleFilter($usersQuery, $user);

        return $usersQuery->get();
    }

    private function applyUserRoleFilter($query, $user)
    {
        switch ($user->role_id) {
            case 4:
                // Solo ve sus propios datos
                $query->where('id', $user->id);
                break;

            case 3:
                // Ve sus propios datos y los datos de usuarios con rol 4 asociados a él
                $query->where('id', $user->id)
                    ->orWhere(function ($query) use ($user) {
                        $query->where('associated_id', $user->id)
                                ->where('role_id', 4);
                    });
                break;

            case 2:
                // Ve sus propios datos, los usuarios con rol 3 asociados a él,
                // y los usuarios con rol 4 asociados a esos usuarios con rol 3
                $query->where('id', $user->id)
                    ->orWhere(function ($query) use ($user) {
                        $query->where('associated_id', $user->id)
                                ->where('role_id', 3);
                    })
                    ->orWhere(function ($query) use ($user) {
                        $query->whereIn('associated_id', function ($subQuery) use ($user) {
                            $subQuery->select('id')
                                    ->from('users')
                                    ->where('associated_id', $user->id)
                                    ->where('role_id', 3);
                        })->where('role_id', 4);
                    });
                break;

            case 1:
                // Sin restricciones, ve todos los usuarios para rol 1
                break;
        }
    }
}
