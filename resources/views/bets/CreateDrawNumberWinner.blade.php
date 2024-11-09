@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Add Winning Number</h2>

    <form action="{{ route('draw_number_winner.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="draw_number">Select Draw Number</label>
            <select name="draw_number" id="draw_number" class="form-control" required>
                @foreach($activeDrawNumbers as $draw)
                    <option value="{{ $draw->draw_number }}">{{ $draw->draw_number }} - {{ $draw->draw_name }}</option>
                @endforeach
            </select>
            @error('draw_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="winning_number">Winning Number</label>
            <input type="number" name="winning_number" id="winning_number" class="form-control" required>
            @error('winning_number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Submit Winning Number</button>
    </form>
</div>
@endsection
