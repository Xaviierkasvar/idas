<?php

namespace App\Http\Controllers;

use App\Models\DrawNumberWinner;
use App\Models\GameConfigurations;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Bet;
use App\Models\PaymentTracking;
use App\Models\User;
use Carbon\Carbon;

class DrawNumberWinnerController extends Controller
{
    // Método para mostrar la vista con los números ganadores
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $winningNumbers = PaymentTracking::whereDate('transaction_date', $date)
            ->with('user', 'associatedUser')
            ->paginate(10);

        return view('bets.DrawNumberWinner', compact('winningNumbers', 'date'));
    }

    public function create()
    {
        $activeDrawNumbers = GameConfigurations::where('is_active', 1)
            ->select('id', 'draw_number', 'draw_name')
            ->get();
        return view('bets.CreateDrawNumberWinner', compact('activeDrawNumbers'));
    }

    public function store(Request $request)
{
    // Validar los datos de entrada
    $request->validate([
        'draw_number' => 'required|integer',
        'winning_number' => 'required|integer',
    ]);

    // Crear un nuevo registro del número ganador en la base de datos
    DrawNumberWinner::create([
        'draw_number' => $request->draw_number,
        'winning_number' => $request->winning_number,
    ]);

    // Desactivar la configuración del juego correspondiente
    GameConfigurations::where('draw_number', $request->draw_number)
        ->update(['is_active' => false]);

    // Llamar a la función para rastrear los ganadores
    $winningCount = $this->trackWinningBets($request->winning_number, $request->draw_number);

    // Mensaje de éxito con el número de ganadores
    if ($winningCount > 0) {
        return redirect()->route('draw-number-winner')->with('success', "Winning number added successfully. Number of winners: $winningCount");
    } else {
        return redirect()->route('draw-number-winner')->with('success', 'Winning number added successfully. No winners for this draw.');
    }
}

public function trackWinningBets($winningNumber, $drawNumber)
{
    $winningBets = Bet::where('draw_number', $drawNumber)
    ->where(function ($query) use ($winningNumber) {
        $query->where('bet_number', $winningNumber)
            ->orWhereRaw("RIGHT(CAST(bet_number AS CHAR), 3) = RIGHT(CAST(? AS CHAR), 3)", [$winningNumber]);
    })
    ->get();


    $winningCount = 0;

    foreach ($winningBets as $bet) {
        PaymentTracking::create([
            'user_id' => $bet->user_id,
            'associated_id' => $bet->associated_id,
            'bet_id' => $bet->bet_id,
            'draw_number' => $bet->draw_number,
            'bet_amount' => $bet->bet_amount,
            'payout_amount' => $this->calculatePayout($bet->bet_amount, $bet->bet_number),
            'transaction_type' => 'PAYOUT',
            'balance' => $this->getUpdatedBalance($bet->user_id, $this->calculatePayout($bet->bet_amount, $bet->bet_number, $winningNumber)),
            'transaction_date' => now(),
        ]);
        $winningCount++;
    }

    return $winningCount;
}

protected function calculatePayout($betAmount, $betNumber)
{
    if (strlen($betNumber) === 4) {
        $payoutMultiplier = 8000;
    }

    elseif (strlen($betNumber) === 3) {
        $payoutMultiplier = 800;
    }
    else {
        return 0;
    }

    return $betAmount * $payoutMultiplier;
}


protected function getUpdatedBalance($userId, $payoutAmount)
{
    $user = User::find($userId);
    return $user->balance + $payoutAmount;
}
}
