@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Create Game Configuration</h2>

    <form action="{{ route('game_configurations.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="draw_number">Draw Number</label>
            <input type="number" name="draw_number" id="draw_number" class="form-control" 
                value="{{ old('draw_number', $nextDrawNumber) }}" readonly>
            @error('draw_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="draw_name">Draw Name</label>
            <input type="text" name="draw_name" id="draw_name" class="form-control" 
                value="{{ old('draw_name', $defaultDrawName) }}" required>
            @error('draw_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="draw_date">Draw Date</label>
            <input type="date" name="draw_date" id="draw_date" class="form-control"
                value="{{ old('draw_date', $defaultDrawDate) }}" 
                min="{{ date('Y-m-d') }}" required>
            @error('draw_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="restricted_numbers">Restricted Numbers</label>
            <div id="restrictedNumbersContainer">
                @foreach ($defaultRestrictedNumbers as $index => $number)
                    <div class="input-group mb-2">
                        <input type="text" name="restricted_numbers[]" class="form-control restricted-number" 
                            value="{{ old('restricted_numbers.' . $index, $number) }}" style="width: 200px;" required>
                        <button type="button" class="btn btn-danger remove-restricted-number" style="margin-left: 5px;">-</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="addRestrictedNumber" class="btn btn-outline-secondary btn-sm mt-2">+</button>
            @error('restricted_numbers.*')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="max_bet_amount">Max Bet Amount</label>
            <input type="number" name="max_bet_amount" id="max_bet_amount" class="form-control" 
                value="{{ old('max_bet_amount', $defaultMaxBetAmount ?? 10000) }}" required>
            @error('max_bet_amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="max_repeats_per_number">Max Repeats Per Number</label>
            <input type="number" name="max_repeats_per_number" id="max_repeats_per_number" class="form-control" 
                value="{{ old('max_repeats_per_number', $defaultMaxRepeatsPerNumber ?? 3) }}" required>
            @error('max_repeats_per_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="seller_margin">Seller Margin</label>
            <input type="number" name="seller_margin" id="seller_margin" class="form-control" 
                value="{{ old('seller_margin', $defaultSeller_margin ?? 10000) }}" required>
            @error('seller_margin')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="closing_time">Closing Time</label>
            <input type="time" name="closing_time" id="closing_time" class="form-control" 
                value="{{ old('closing_time', $defaultClosingTime ) }}" required>
            @error('closing_time')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save Configuration</button>
    </form>
</div>

<!-- JavaScript para agregar y eliminar campos de números restringidos -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addRestrictedNumberButton = document.getElementById('addRestrictedNumber');
    const restrictedNumbersContainer = document.getElementById('restrictedNumbersContainer');

    addRestrictedNumberButton.addEventListener('click', function () {
        const newInputGroup = document.createElement('div');
        newInputGroup.classList.add('input-group', 'mb-2');
        
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'restricted_numbers[]';
        newInput.classList.add('form-control', 'restricted-number');
        newInput.style.width = '200px';
        newInput.required = true;
        
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.classList.add('btn', 'btn-danger', 'remove-restricted-number');
        removeButton.style.marginLeft = '5px';
        removeButton.textContent = '-';

        removeButton.addEventListener('click', function () {
            restrictedNumbersContainer.removeChild(newInputGroup);
        });

        newInputGroup.appendChild(newInput);
        newInputGroup.appendChild(removeButton);
        restrictedNumbersContainer.appendChild(newInputGroup);
    });

    restrictedNumbersContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-restricted-number')) {
            const inputGroup = event.target.parentElement;
            restrictedNumbersContainer.removeChild(inputGroup);
        }
    });
});
</script>
@endsection
