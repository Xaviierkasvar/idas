<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BetControl;
use App\Models\DrawNumberWinner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ChiefController extends Controller
{
    public function index()
    {
        $chiefId = Auth::id();
        $today = Carbon::today();

        // Obtener todos los IDs de usuarios asociados al chief de forma recursiva
        $userIds = $this->getRecursiveAssociatedIds($chiefId);

        // Consultar las ventas del día, semana y mes para todos los usuarios obtenidos
        $salesOfTheDay = BetControl::whereIn('user_id', $userIds)
            ->whereDate('created_at', $today)
            ->sum('total_bet_amount');

        $salesOfTheWeek = BetControl::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])
            ->sum('total_bet_amount');

        $salesOfTheMonth = BetControl::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()
            ])
            ->sum('total_bet_amount');

        // Obtener los números ganadores de la última semana para todos los usuarios obtenidos
        $winningNumbers = DrawNumberWinner::whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])
            ->with('gameConfiguration')
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'draw_number', 'winning_number']);

        return view('chief.dashboard', [
            'title' => 'Dashboard Chief',
            'salesOfTheDay' => $salesOfTheDay,
            'salesOfTheWeek' => $salesOfTheWeek,
            'salesOfTheMonth' => $salesOfTheMonth,
            'winningNumbers' => $winningNumbers,
        ]);
    }

    /**
     * Función recursiva para obtener todos los IDs de usuarios asociados.
     */
    private function getRecursiveAssociatedIds($userId)
    {
        // Inicializamos el array con el ID del usuario actual
        $userIds = collect([$userId]);

        // Obtenemos los IDs de usuarios asociados directamente al usuario actual
        $associatedIds = User::where('associated_id', $userId)->pluck('id');

        // Para cada usuario asociado, llamamos recursivamente a la función
        foreach ($associatedIds as $associatedId) {
            $userIds = $userIds->merge($this->getRecursiveAssociatedIds($associatedId));
        }

        // Devolvemos los IDs sin duplicados
        return $userIds->unique()->toArray();
    }
}
