<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\GameConfigurations;
use App\Models\BetControl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BetController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');
        $activeConfigurations = GameConfigurations::where('draw_date', $today)
            ->where('is_active', true)
            ->get();

        return view('bets.index', compact('activeConfigurations'));
    }

    public function create()
    {
        return view('bets.create');
    }

    public function store(Request $request)
    {
        $gameConfig = GameConfigurations::find($request->draw_number);

        if ($gameConfig) {
            if ($gameConfig->is_active) {
                $currentDateTime = now();
                $closingTime = Carbon::parse($gameConfig->closing_time);

                if ($currentDateTime->greaterThanOrEqualTo($closingTime)) {
                    $gameConfig->updateActiveStatus();
                    return response()->json(['error' => 'Configuración de sorteo inactiva.'], 200);
                } else {
                    return $this->store_post($request, $gameConfig);
                }
            } else {
                return response()->json(['error' => 'Configuración de sorteo inactiva.'], 400);
            }
        } else {
            return response()->json(['error' => 'Configuración de sorteo no encontrada.'], 404);
        }
    }

    public function store_post($request, $gameConfig)
    {
        $user = Auth::user();
        $currentDate = now();

        // Validate the request data
        $this->validateBetRequest($request);

        // Set up config values
        $maxBetAmount = $gameConfig->max_bet_amount;
        $maxRepeatsPerNumber = $gameConfig->max_repeats_per_number;
        $restrictedNumbers = $this->getRestrictedNumbers($gameConfig);

        // Check each bet amount against max bet amount
        $this->checkBetAmounts($request->bet_amount, $maxBetAmount);
        
        // Count existing bets to check repetitions
        $betCounts = $this->getExistingBetCounts($request->draw_number, $request->bet_number);

        // Validate each bet number and repetition limits
        $this->checkBetsRestrictions($request->bet_number, $restrictedNumbers, $betCounts, $maxRepeatsPerNumber);

        // Save bets in the database and get the data for response
        $betsDataSingle = $this->createBets($request, $user, $currentDate, $betCounts);

        // Calculate and store control record
        $this->calculateAndStoreControlRecord($request->draw_number, $betsDataSingle['totalBetAmount'], $user->id, $user->associated_id);

        // Retornar una respuesta de éxito con los datos de las apuestas
        return response()->json(['success' => 'Apuestas creadas exitosamente.', 'data' => $betsDataSingle['betsData']], 201);    
    }



    private function calculateAndStoreControlRecord($drawNumber, $totalBetAmount, $userId, $associatedId)
    {
        // Obtener el margen del vendedor para el draw_number
        $sellerMarginPercentage = GameConfigurations::where('draw_number', $drawNumber)->value('seller_margin');
        
        // Calcular la ganancia del vendedor y de la casa
        $sellerMargin = ($sellerMarginPercentage / 100) * $totalBetAmount;
        $houseMargin = $totalBetAmount - $sellerMargin;

        // Crear el registro en la tabla de control
        BetControl::create([
            'user_id' => $userId,
            'associated_id' => $associatedId,
            'draw_number' => $drawNumber,
            'total_bet_amount' => $totalBetAmount,
            'seller_margin' => $sellerMargin,
            'house_margin' => $houseMargin,
        ]);
    }

    private function validateBetRequest($request)
    {
        try {
            $request->validate([
                'draw_number' => 'required|integer|exists:game_configurations,draw_number',
                'bet_number' => 'required|array',
                'bet_amount' => 'required|array',
                'bet_amount.*' => 'required|integer|min:50',
            ]);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $error) {
                Log::error("Error de validación en el campo '$field': " . implode(", ", $error));
                $errors[] = implode(", ", $error);
            }
            response()->json(['error' => $errors], 400)->throwResponse();
        }
    }


    private function getRestrictedNumbers($gameConfig)
    {
        $restrictedNumbers = is_array($gameConfig->restricted_numbers) 
            ? $gameConfig->restricted_numbers 
            : json_decode($gameConfig->restricted_numbers, true);

        if (is_array($restrictedNumbers)) {
            return array_map(fn($number) => preg_replace('/\D/', '', $number), $restrictedNumbers);
        }
        return [];
    }

    private function checkBetAmounts($betAmounts, $maxBetAmount)
    {
        foreach ($betAmounts as $amount) {
            Log::info("Revisando monto de apuesta: $amount con máximo permitido: $maxBetAmount");
            if ($amount > $maxBetAmount) {
                response()->json(['error' => "El monto de la apuesta no puede exceder $maxBetAmount."], 400)->throwResponse();
            }
        }
    }

    private function getExistingBetCounts($drawNumber, $betNumbers)
    {
        $existingBets = Bet::where('draw_number', $drawNumber)
            ->whereIn('bet_number', $betNumbers)
            ->select('bet_number', DB::raw('count(*) as count'))
            ->groupBy('bet_number')
            ->get();

        $betCounts = [];
        foreach ($existingBets as $bet) {
            $betCounts[$bet->bet_number] = $bet->count;
        }
        return $betCounts;
    }

    private function checkBetsRestrictions($betNumbers, $restrictedNumbers, &$betCounts, $maxRepeatsPerNumber)
    {
        foreach ($betNumbers as $betNumber) {
            if (in_array($betNumber, $restrictedNumbers)) {
                response()->json(['error' => "El número $betNumber está restringido."], 400)->throwResponse();
            }
            $betCounts[$betNumber] = ($betCounts[$betNumber] ?? 0) + 1;
            if ($betCounts[$betNumber] > $maxRepeatsPerNumber) {
                response()->json(['error' => "El número $betNumber no puede ser apostado más de $maxRepeatsPerNumber veces."], 400)->throwResponse();
            }
        }
    }

    private function createBets($request, $user, $currentDate, $betCounts)
    {
        $bet_id = Bet::max('bet_id') + 1;
        $betsData = [];
        $totalBetAmount = 0;
        $betsDataSingle = [];

        try {
            foreach ($request->bet_number as $index => $betNumber) {
                $totalBetAmount += $request->bet_amount[$index];
                $bet = Bet::create([
                    'bet_id' => $bet_id,
                    'draw_number' => $request->draw_number,
                    'bet_number' => $betNumber,
                    'bet_amount' => $request->bet_amount[$index],
                    'user_id' => $user->id,
                    'associated_id' => $user->associated_id,
                    'bet_date_time' => $currentDate,
                ]);

                // Limpiar betsData antes de agregar una nueva entrada
                $betsData = [];
                $betsData[$betNumber] = $request->bet_amount[$index];

                $betsDataSingle[] = [
                    'bet_id' => $bet->bet_id,
                    'draw_number' => $bet->draw_number,
                    'bet_number_and_bet_amount' => $betsData,
                    'bet_date_time' => $currentDate->toDateTimeString(),
                ];
            }

            return [
                'totalBetAmount' => $totalBetAmount,
                'betsData' => $betsDataSingle
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear las apuestas: ' . $e->getMessage());
            response()->json(['error' => 'Hubo un problema al crear las apuestas. Inténtelo de nuevo.'], 500)->throwResponse();
        }
    }
}
