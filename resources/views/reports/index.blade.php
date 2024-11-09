@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Bet Reports</h2>

    <!-- Filter Form -->
    <form action="{{ route('reports.filter') }}" method="POST">
        @csrf

        <!-- Date Range -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request()->start_date ?? now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request()->end_date ?? now()->format('Y-m-d') }}" required>
            </div>
        </div>

        <!-- User Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="bet_number">Bet Number (optional):</label>
                <input type="number" id="bet_number" name="bet_number" class="form-control" value="{{ request()->bet_number }}" placeholder="Enter Bet Number" min="0" max="9999" pattern="\d{1,4}">
            </div>
            <div class="col-md-4">
                <label for="user_id">User (optional):</label>
                <select id="user_id" name="user_id" class="form-control">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request()->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Button -->
        <button type="submit" class="btn btn-primary">Filter Reports</button>
    </form>

    <!-- Total Bet Amount Display and Print Button -->
    <div class="row mb-4">
        <div class="col-md-6 text-sm-center text-lg-end">
            <strong>Total Bet Amount:</strong> ${{ number_format($totalBetAmount, 0, '.', '.') }}
        </div>
        <div class="col-md-6 text-end">
            <button id="printButton" class="btn btn-secondary">Print Page</button>
        </div>
    </div>

    <!-- Bet Results -->
    <table class="table">
        <thead>
            <tr>
                <th>Associated Name</th>
                <th>Bet Number</th>
                <th>Bet Amount</th>
                <th>Payout Amount</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bets as $bet)
                <tr>
                    <td>{{ $bet->user ? $bet->user->name : 'Unknown' }}</td>
                    <td>{{ $bet->bet_id }}</td>
                    <td>{{ $bet->bet_number }}</td>
                    <td>${{ number_format($bet->bet_amount, 0, '.', '.') }}</td>
                    <td>{{ $bet->bet_date_time  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <ul class="pagination">
            {{ $bets->links('pagination::bootstrap-4') }}
        </ul>
    </div>
</div>


@vite('resources/js/reports.js')
@endsection
