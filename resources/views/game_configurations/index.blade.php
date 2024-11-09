@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Game Configurations</h2>

    <!-- Search Filter by Closing Time -->
    <div class="mb-3">
        <form action="{{ route('game_configurations.index') }}" method="GET" id="searchForm">
            <input type="date" id="searchInput" name="search_date" value="{{ old('search_date', $searchDate) }}" class="form-control" placeholder="Search by closing time">
        </form>
    </div>

    <!-- Button to Create New Game Configuration -->
    <div class="mb-3">
        <a href="{{ route('game_configurations.create') }}" class="btn btn-primary">Create New Configuration</a>
    </div>

    <!-- Table of Configurations -->
    <table class="table" id="configurationsTable">
        <thead>
            <tr>
                <th>Draw Number</th>
                <th>Draw Name</th>
                <th>Restricted Numbers</th>
                <th>Max Bet Amount</th>
                <th>Max Repeats Per Number</th>
                <th>Draw Date</th>
                <th>Closing Time</th>
                <th>Seller Margin</th>
                <th>Is Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($configurations as $configuration)
                <tr>
                    <td>{{ $configuration->draw_number }}</td>
                    <td>{{ $configuration->draw_name }}</td>
                    <td>
                        @php
                            $restrictedNumbers = json_decode($configuration->restricted_numbers);
                            $formattedNumbers = is_array($restrictedNumbers) 
                                ? implode('-', array_map(function($number) {
                                    return preg_replace('/\D/', '', $number);
                                }, $restrictedNumbers)) 
                                : '';
                        @endphp
                        {{ $formattedNumbers }}
                    </td>
                    <td>{{ $configuration->max_bet_amount }}</td>
                    <td>{{ $configuration->max_repeats_per_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($configuration->draw_date)->format('Y-m-d') }}</td>
                    <td>{{ $configuration->closing_time }}</td>
                    <td>{{ $configuration->seller_margin }}</td>
                    <td>{{ $configuration->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('game_configurations.edit', $configuration) }}" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>        
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <ul class="pagination">
            {{ $configurations->appends(['search_date' => request()->search_date])->links('pagination::bootstrap-4') }}
        </ul>
    </div>
</div>

<!-- JavaScript for filtering and pagination -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    searchInput.addEventListener('change', function () {
        searchForm.submit();
    });
});
</script>
@endsection
