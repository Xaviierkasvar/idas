@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>Edit Profile</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">First Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (optional)</label>
            <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password (optional)</label>
            <input type="password" class="form-control" name="password_confirmation" placeholder="Leave blank to keep current password">
        </div>

        <button type="submit" class="btn btn-primary">Update Information</button>
    </form>
</div>
@endsection
