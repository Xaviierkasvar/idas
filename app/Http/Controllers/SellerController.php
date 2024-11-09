<?php

// App\Http\Controllers\SellerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BetControl;
use App\Models\DrawNumberWinner;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $salesOfTheDay = BetControl::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->sum('total_bet_amount');

        $salesOfTheWeek = BetControl::where('user_id', $userId)
            ->whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])->sum('total_bet_amount');

        $salesOfTheMonth = BetControl::where('user_id', $userId)
            ->whereBetween('created_at', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()
            ])->sum('total_bet_amount');

        // Obtener los números ganadores de la última semana con el nombre del sorteo
        $winningNumbers = DrawNumberWinner::whereBetween('created_at', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek()
            ])
            ->with('gameConfiguration') // Cargar la relación para obtener draw_name
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'draw_number', 'winning_number']);

        return view('seller.dashboard', [
            'title' => 'Dashboard Seller',
            'salesOfTheDay' => $salesOfTheDay,
            'salesOfTheWeek' => $salesOfTheWeek,
            'salesOfTheMonth' => $salesOfTheMonth,
            'winningNumbers' => $winningNumbers,
        ]);
    }
}
