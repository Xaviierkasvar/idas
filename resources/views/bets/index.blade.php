@extends('layouts.app')

@section('content')
<div class="page-container">
    <div class="d-flex align-items-center">
        <h1 class="me-3">Place Your Bets</h1>
        <div class="d-flex justify-content-between align-items-end">
            <label for="seller_margin" class="col form-label">Seller Margin</label>
            <input type="text" id="seller_margin" class="col form-control" readonly>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                openSuccessModal(@json(session('success_data')));
            });
        </script>
    @endif
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

<!-- Modal HTML -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Bet Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="betDetailsTable">
                    <thead>
                        <tr>
                            <th>Draw Number</th>
                            <th>Bet Number</th>
                            <th>Bet Amount</th>
                            <th>User ID</th>
                            <th>Associated ID</th>
                            <th>Bet Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printBetDetails">Print</button>
            </div>
        </div>
    </div>
</div>

@vite('resources/js/bets.js')
@endsection
