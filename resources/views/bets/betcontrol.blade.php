@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Bet Wallet</h1>

    <!-- Display daily totals with responsive layout -->
    <div class="alert alert-info">
        <div class="row text-center">
            <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                <strong>Total Bets of the Day:</strong> ${{ number_format($totalBetAmount, 0, '', '.') }}
            </div>
            <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                <strong>Total Seller Margin:</strong> ${{ number_format($totalSellerMargin, 0, '', '.') }}
            </div>
            <div class="col-12 col-sm-4">
                <strong>Total House Margin:</strong> ${{ number_format($totalHouseMargin, 0, '', '.') }}
            </div>
        </div>
    </div>

    <!-- Inputs ocultos para almacenar los totales -->
    <input type="hidden" id="totalBets" value="{{ $totalBetAmount }}">
    <input type="hidden" id="totalSellerMargin" value="{{ $totalSellerMargin }}">
    <input type="hidden" id="totalHouseMargin" value="{{ $totalHouseMargin }}">

    <!-- Filter form -->
    <form action="{{ route('betcontrol.filter') }}" method="GET">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') ?? now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') ?? now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="user_id">User:</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">All</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Total Bet Amount Display and Print Button -->
    <div class="row mb-4">
        <div class="col text-end">
            <button id="printButton" class="btn btn-secondary">Print Page</button>
        </div>
    </div>

    <!-- Bets control table -->
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">User</th>
                <th class="text-center">Creation Date</th>
                <th class="text-center">Total Bet Amount</th>
                <th class="text-center">Seller Margin</th>
                <th class="text-center">House Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($betControls as $betControl)
                <tr>
                    <td class="text-center">{{ $betControl->id }}</td>
                    <td class="text-center">{{ $betControl->user->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $betControl->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">${{ number_format($betControl->total_bet_amount, 0, '', '.') }}</td>
                    <td class="text-center">${{ number_format($betControl->seller_margin, 0, '', '.') }}</td>
                    <td class="text-center">${{ number_format($betControl->house_margin, 0, '', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <ul class="pagination">
            {{ $betControls->links('pagination::bootstrap-4') }}
        </ul>
    </div>
</div>
@vite('resources/js/betcontrol.js')
@endsection
