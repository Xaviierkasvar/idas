<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }}</title>
    
    <!-- Enlaza los archivos compilados por Vite -->
    @vite(['resources/js/app.js', 'resources/sass/layouts/dashboard.scss', 'resources/sass/app.scss'])
</head>
<body>
    @include('layouts.navbar')

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
