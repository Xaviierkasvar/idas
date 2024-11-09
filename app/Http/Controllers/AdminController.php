<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BetControl;
use App\Models\DrawNumberWinner;

class AdminController extends Controller
{
    public function index()
    {
        // Obtener todas las ventas sin aplicar filtros de tiempo
        $totalSales = BetControl::sum('total_bet_amount');

        // Total de ventas del día, semana y mes sin filtros
        $salesOfTheDay = BetControl::whereDate('created_at', now())->sum('total_bet_amount');
        $salesOfTheWeek = BetControl::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->sum('total_bet_amount');
        $salesOfTheMonth = BetControl::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->sum('total_bet_amount');

        // Obtener todos los números ganadores sin filtros de tiempo
        $winningNumbers = DrawNumberWinner::with('gameConfiguration') // Relación para obtener el nombre del sorteo
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'draw_number', 'winning_number']);

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'totalSales' => $totalSales,
            'salesOfTheDay' => $salesOfTheDay,
            'salesOfTheWeek' => $salesOfTheWeek,
            'salesOfTheMonth' => $salesOfTheMonth,
            'winningNumbers' => $winningNumbers,
        ]);
    }
}
