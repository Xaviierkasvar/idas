@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Winning Numbers</h2>

    <!-- Search Filter by Draw Date -->
    <div class="mb-3">
        <input type="date" id="searchInput" class="form-control" placeholder="Search by draw date" value="{{ request('date', $date) }}">
    </div>

    <!-- Button to Add New Winning Number -->
    @if(in_array(Auth::user()->role_id, [1, 2]))
        <div class="mb-3">
            <a href="{{ route('draw_number_winner.create') }}" class="btn btn-primary">Add Winning Number</a>
        </div>
    @endif

    <!-- Winners Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Seller Name</th>
                <th>Bet Number</th>
                <th>Bet Amount</th>
                <th>Payout Amount</th>
                <th>Transaction Type</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($winningNumbers as $record)
                <tr>
                    <td>{{ $record->user ? $record->user->name : 'Unknown' }}</td>
                    <td>{{ $record->bet_id }}</td>
                    <td>${{ number_format($record->bet_amount, 2) }}</td>
                    <td>${{ number_format($record->payout_amount, 0, '.', '.') }}</td> <!-- Thousand separator -->
                    <td>{{ $record->transaction_type }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->transaction_date)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $winningNumbers->appends(['date' => $date])->links() }}
    </div>
</div>

<!-- SweetAlert para mostrar mensajes de éxito -->
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

<!-- JavaScript para escuchar cambios en el campo de fecha y enviar la solicitud GET -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('change', function () {
        const selectedDate = searchInput.value;
        const url = "{{ route('draw-number-winner') }}?date=" + selectedDate;
        window.location.href = url;
    });
});
</script>
@endsection
