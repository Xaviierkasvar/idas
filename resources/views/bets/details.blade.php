@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success text-center">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('betsData'))
        <div class="bets-details">
            <h3>Detalles de las Apuestas</h3>
            <pre>{{ print_r(session('betsData'), true) }}</pre>
            <!-- O puedes formatear y mostrar los datos de apuestas como desees -->
        </div>
    @endif
@endsection
