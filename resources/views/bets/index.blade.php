@extends('layouts.app')

@section('content')
<div id="bet-view" class="page-container">
    <div class="d-flex align-items-center">
        <h1 class="me-3">Place Your Bets</h1>
        <div class="d-flex justify-content-between align-items-end">
            <label for="seller_margin" class="col form-label">Seller Margin</label>
            <input type="text" id="seller_margin" class="col form-control" readonly>
        </div>
    </div>
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            window.validationErrors = @json($errors->all());
        </script>
    @endif

    <form id="betForm" action="{{ route('bets.store') }}" method="POST">
        @csrf    
        <div class="mb-3">
            <label for="draw_number" class="form-label">Select Draw Number</label>
            <select id="draw_number" name="draw_number" class="form-select" required>
                <option value="">-- Select Draw Number --</option>
                @foreach ($activeConfigurations as $configuration)
                    <option value="{{ $configuration->draw_number }}" 
                            data-seller-margin="{{ $configuration->seller_margin }}" 
                            {{ old('draw_number') == $configuration->draw_number ? 'selected' : '' }}>
                        {{ $configuration->draw_number . ' - ' . $configuration->draw_name }}
                    </option>
                @endforeach
            </select>
            @error('draw_number')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div id="betRows">
            @foreach (old('bet_number', ['']) as $index => $betNumber)
                <div class="bet-row mb-3">
                    <div class="input-group">
                        <input type="number" class="form-control" name="bet_number[]" placeholder="Enter Bet Number" value="{{ old('bet_number.'.$index) }}" min="0" max="9999" pattern="\d{1,4}" required>
                        <input type="number" class="form-control bet-amount" name="bet_amount[]" placeholder="Enter Bet Amount" value="{{ old('bet_amount.'.$index) }}" required>
                        <button type="button" class="btn btn-danger remove-row" style="margin-left: 5px;">-</button>
                    </div>
                    @error('bet_number.' . $index)
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('bet_amount.' . $index)
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-between w-100 mt-3">
            <button type="button" class="btn btn-success" id="addRow" style="height: 40px; margin-top: 4%; padding-top: 0%;">+</button>
            <div class="row mx-2">
                <div class="col d-flex align-items-center">
                    <label for="seller_margin_value" class="form-label">Seller Margin Value</label>
                    <input type="text" id="seller_margin_value" class="form-control" value="0" readonly>
                </div>
                <div class="col d-flex align-items-center">
                    <label for="total_bet_amount" class="form-label">Total Bet Amount</label>
                    <input type="text" id="total_bet_amount" class="form-control" value="0" readonly>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 50px; margin-top: 3%; padding-top: 0%;">Place Bets</button>
        </div>        
    </form>
</div>

<div id="successful-view" class="d-none text-center">
    <div class="d-flex justify-content-center align-items-center mb-3">
        <h1 class="me-3">Bet Details</h1>
    </div>

    <!-- Bet ID and Draw Number outside the table -->
    <div class="d-flex justify-content-center mb-4">
        <div class="me-5">
            <p><strong>Bet ID:</strong> <span id="betId">73</span></p>
        </div>
        <div>
            <p><strong>Draw Number:</strong> <span id="drawNumber">7</span></p>
        </div>
    </div>

    <!-- Table for Bet Number and Bet Amount -->
    <table class="table mx-auto" style="width: 80%">
        <thead>
            <tr>
                <th>Bet Number</th>
                <th>Bet Amount</th>
            </tr>
        </thead>
        <tbody id="betDetailsTableBody">
            <!-- Data will be dynamically inserted here -->
        </tbody>
    </table>

    <!-- Bet Date & Time and Total Amount -->
    <div class="col d-flex justify-content-between mt-3">
        <div class="col-6">
            <p><strong>Bet Date & Time:</strong> <span id="betDateTime">2024-11-16 15:08:09</span></p>
        </div>
        <div class="col-6 text-start">
            <p><strong>Total Bet Amount:</strong> <span id="totalAmount">0</span></p>
        </div>
    </div>

    <div class="modal-footer">
        <a href="{{ route('bets.index') }}">
            <button type="button" class="btn btn-secondary">Close</button>
        </a>
    </div>
</div>
@vite('resources/js/bets.js')
@endsection
