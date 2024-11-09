<?php

// App\Http\Controllers\LeaderController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BetControl;
use App\Models\DrawNumberWinner;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LeaderController extends Controller
{
    public function index()
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
        $today = Carbon::today();

        // Obtener el usuario actual
        $user = User::find($userId);

        // Consultar las ventas del usuario y sus asociados
        $salesOfTheDay = BetControl::whereIn('user_id', $this->getUserAndAssociates($userId))
            ->whereDate('created_at', $today)
            ->sum('total_bet_amount');

        $salesOfTheWeek = BetControl::whereIn('user_id', $this->getUserAndAssociates($userId))
            ->whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])
            ->sum('total_bet_amount');

        $salesOfTheMonth = BetControl::whereIn('user_id', $this->getUserAndAssociates($userId))
            ->whereBetween('created_at', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()
            ])
            ->sum('total_bet_amount');

        // Obtener los números ganadores de la última semana, incluyendo los relacionados
        $winningNumbers = DrawNumberWinner::whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])
            ->with('gameConfiguration') // Cargar la relación para obtener draw_name
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'draw_number', 'winning_number']);

        // Retornar la vista con los datos
        return view('leader.dashboard', [
            'title' => 'Dashboard Leader',
            'salesOfTheDay' => $salesOfTheDay,
            'salesOfTheWeek' => $salesOfTheWeek,
            'salesOfTheMonth' => $salesOfTheMonth,
            'winningNumbers' => $winningNumbers,
        ]);
    }

    // Función para obtener el ID del usuario y sus asociados
    private function getUserAndAssociates($userId)
    {
        // Obtener el usuario y los asociados a través de associated_id
        return User::where('associated_id', $userId)
            ->pluck('id')
            ->push($userId); // Incluye el ID del usuario actual
    }
}
