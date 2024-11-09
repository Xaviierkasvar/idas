@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Edit Game Configuration</h2>

    @if (isset($configuration))
        @php
            $currentTime = now()->format('H:i');
            $closingTimePassed = $configuration->closing_time < $currentTime;

            $restrictedNumbers = is_array($configuration->restricted_numbers) 
                ? $configuration->restricted_numbers 
                : explode(',', $configuration->restricted_numbers);
        @endphp

        @if ($closingTimePassed)
            <div class="alert alert-danger" role="alert">
                You cannot edit this configuration as the closing time has already passed.
            </div>
        @else
            <form action="{{ route('game_configurations.update', $configuration->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="draw_number">Draw Number</label>
                    <input type="number" name="draw_number" id="draw_number" class="form-control"
                        value="{{ old('draw_number', $configuration->draw_number) }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="draw_name">Draw Name</label>
                    <input type="text" name="draw_name" id="draw_name" class="form-control"
                        value="{{ old('draw_name', $configuration->draw_name) }}">
                </div>

                <div class="mb-3">
                    <label for="draw_date">Draw Date</label>
                    <input type="date" name="draw_date" id="draw_date" class="form-control"
                        value="{{ old('draw_date', $configuration->draw_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-3">
                    <label for="max_bet_amount">Max Bet Amount</label>
                    <input type="number" name="max_bet_amount" id="max_bet_amount" class="form-control"
                        value="{{ old('max_bet_amount', $configuration->max_bet_amount) }}" required>
                </div>

                <div class="mb-3">
                    <label for="max_repeats_per_number">Max Repeats Per Number</label>
                    <input type="number" name="max_repeats_per_number" id="max_repeats_per_number" class="form-control"
                        value="{{ old('max_repeats_per_number', $configuration->max_repeats_per_number) }}" required>
                </div>

                <div class="mb-3">
                    <label for="restricted_numbers">Restricted Numbers</label>
                    <div id="restrictedNumbersContainer">
                        @foreach ($restrictedNumbers as $index => $number)
                            <div class="input-group mb-2">
                                <input type="text" name="restricted_numbers[]" class="form-control restricted-number" 
                                    value="{{ old('restricted_numbers.' . $index, preg_replace('/\D/', '', $number)) }}" style="width: 200px;">
                                <button type="button" class="btn btn-danger remove-restricted-number" style="margin-left: 5px;">-</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="addRestrictedNumber" class="btn btn-outline-secondary btn-sm mt-2">+</button>
                    @error('restricted_numbers')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="seller_margin">Seller Margin</label>
                    <input type="number" name="seller_margin" id="seller_margin" class="form-control"
                        value="{{ old('seller_margin', $configuration->seller_margin) }}" required>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                        {{ $configuration->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active Configuration</label>
                </div>

                <button type="submit" class="btn btn-success">Update Configuration</button>
            </form>
        @endif
    @else
        <div class="alert alert-warning" role="alert">
            No configuration found for editing.
        </div>
    @endif
</div>

<script>
    document.getElementById('addRestrictedNumber').addEventListener('click', function() {
        const container = document.getElementById('restrictedNumbersContainer');
        const newInput = document.createElement('div');
        newInput.className = 'input-group mb-2';
        newInput.innerHTML = `
            <input type="text" name="restricted_numbers[]" class="form-control restricted-number" style="width: 200px;">
            <button type="button" class="btn btn-danger remove-restricted-number" style="margin-left: 5px;">-</button>
        `;
        container.appendChild(newInput);
    });

    document.getElementById('restrictedNumbersContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-restricted-number')) {
            e.target.parentElement.remove();
        }
    });
</script>
@endsection
