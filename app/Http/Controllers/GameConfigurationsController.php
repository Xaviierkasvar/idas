<?php

namespace App\Http\Controllers;

use App\Models\GameConfigurations;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GameConfigurationsController extends Controller
{
    public function index(Request $request)
    {
        $searchDate = $request->input('search_date', today()->toDateString()); // Establece la fecha actual si no se pasa un parámetro

        $query = GameConfigurations::query();

        if ($searchDate) {
            $query->whereDate('closing_time', $searchDate); // Filtrar por fecha
        }

        $configurations = $query->paginate(10); // Paginación de 10 elementos por página

        return view('game_configurations.index', compact('configurations', 'searchDate'));
    }

    public function create()
    {
        $lastConfiguration = GameConfigurations::orderBy('draw_number', 'desc')->first();

        $nextDrawNumber = $lastConfiguration ? $lastConfiguration->draw_number + 1 : 1;

        $defaultRestrictedNumbers = $lastConfiguration && is_string($lastConfiguration->restricted_numbers)
            ? json_decode($lastConfiguration->restricted_numbers, true) // Decodificar si es una cadena JSON
            : [];

            $defaultDrawDate = $lastConfiguration && $lastConfiguration->draw_date 
            ? \Carbon\Carbon::parse($lastConfiguration->draw_date)->format('Y-m-d') 
            : now()->format('Y-m-d');
        $defaultDrawName = $lastConfiguration ? $lastConfiguration->draw_name : 'Draw Name';
        $defaultMaxBetAmount = $lastConfiguration ? $lastConfiguration->max_bet_amount : 10000;
        $defaultMaxRepeatsPerNumber = $lastConfiguration ? $lastConfiguration->max_repeats_per_number : 3;
        $defaultClosingTime = $lastConfiguration ? $lastConfiguration->closing_time : '21:15';
        $defaultSeller_margin = $lastConfiguration ? $lastConfiguration->seller_margin : 10;

        return view('game_configurations.create', compact(
            'nextDrawNumber',

            'defaultDrawName',
            'defaultDrawDate',
            'lastConfiguration',
            'defaultRestrictedNumbers',
            'defaultMaxBetAmount',
            'defaultMaxRepeatsPerNumber',
            'defaultClosingTime',
            'defaultSeller_margin'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'draw_number' => 'required|unique:game_configurations,draw_number',
            'draw_name' => 'required|string',
            'restricted_numbers' => 'nullable|array',
            'restricted_numbers.*' => 'integer',
            'max_bet_amount' => 'required|integer',
            'max_repeats_per_number' => 'required|integer',
            'closing_time' => 'required|date_format:H:i',
            'draw_date' => 'required|date|after_or_equal:today',
            'seller_margin' => 'required|integer',
        ]);
        
        GameConfigurations::create([
            'draw_number' => $request->draw_number,
            'draw_name' => $request->draw_name,
            'restricted_numbers' => json_encode($request->restricted_numbers), // Convertir a JSON
            'max_bet_amount' => $request->max_bet_amount,
            'max_repeats_per_number' => $request->max_repeats_per_number,
            'closing_time' => $request->closing_time,
            'draw_date' => $request->draw_date,
            'seller_margin' => $request->seller_margin,
            'is_active' => true,
        ]);

        return redirect()->route('game_configurations.index')->with('success', 'Game configuration created successfully.');
    }

    public function edit($id)
    {
        $configuration = GameConfigurations::findOrFail($id);
        return view('game_configurations.edit', compact('configuration'));
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'draw_date' => 'required|date',
            'draw_name' => 'required|string',
            'restricted_numbers' => 'nullable|array',
            'max_bet_amount' => 'required|numeric',
            'max_repeats_per_number' => 'required|integer',
            'seller_margin' => 'required|integer',
        ]);
        
        $validatedData['is_active'] = $request->has('is_active');
        $validatedData['restricted_numbers'] = json_encode($request->restricted_numbers);

        $configuration = GameConfigurations::findOrFail($id);
        $configuration->update($validatedData);

        return redirect()->route('game_configurations.index')->with('success', 'Configuration updated successfully.');
    }


}
