@extends('layouts.app')

@section('content')
<div class="container">
    <h2>User Details</h2>
    <div class="card">
        <div class="card-header">
            {{ $user->name }}
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role->name }}</p>
            <p><strong>Created At:</strong> {{ $user->created_at }}</p>
            <p><strong>Updated At:</strong> {{ $user->updated_at }}</p>
            <a href="{{ route('users.index') }}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div>
@endsection
