@extends('layouts.app')

@section('content')
@vite(['resources/sass/layouts/dashboard.scss'])
<div class="dashboard-content">
    <div class="dashboard-cards row">
        <div class="col-md-3 mb-4">
            <div class="card text-center shadow border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">Sales of the Day</h5>
                    <p class="card-text text-success" style="font-size: 1.5rem;">${{ number_format($salesOfTheDay, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center shadow border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">Sales of the Week</h5>
                    <p class="card-text text-success" style="font-size: 1.5rem;">${{ number_format($salesOfTheWeek, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-center shadow border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">Sales of the Month</h5>
                    <p class="card-text text-success" style="font-size: 1.5rem;">${{ number_format($salesOfTheMonth, 2) }}</p>
                </div>
            </div>
        </div>
        <!-- Números ganadores de la última semana -->
        <div class="col-md-5 mb-4">
            <div class="card shadow border-0 wide-card">
                <div class="card-body">
                    <h5 class="card-title text-primary">Winning Numbers This Week</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Draw Number</th>
                                <th>Winning Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($winningNumbers as $winner)
                                <tr>
                                    <td>{{ $winner->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $winner->gameConfiguration->draw_name }}</td>
                                    <td>{{ $winner->winning_number }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@vite('resources\sass\admin\dashboard.css')
@endsection
