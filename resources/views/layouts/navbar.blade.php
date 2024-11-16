<!-- resources/views/layouts/navbar.blade.php -->

<nav id="navbar" class="navbar">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" id="menuToggle" onclick="toggleMenu()">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="btn" onclick="window.history.back();" style="color: inherit;">
            <i class="bi bi-arrow-left"></i> Back
        </button>        
        <h1 class="navbar-title">{{ $title ?? 'Dashboard' }}</h1>
        
        <div class="current-date-time">
            {{ \Carbon\Carbon::now()->format('l, d M Y H:i') }}
        </div>

        @php
            $firstName = session('name', '');
            $lastName = session('last_name', '');
            $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        @endphp
        
        <div class="user-logo">{{ $initials }}</div>
    </div>
</nav>

<!-- Menú Lateral -->
<div class="sidebar" id="sidebar">
    <button class="close-menu" onclick="toggleMenu()">✖</button>
    <ul>
        <li>
            <a href="{{ route('home') }}">
                <span class="menu-item">Home</span>
            </a>
        </li>
        <li>
            <a href="{{ route('bets.index') }}">
                <span class="menu-item">betting</span>
            </a>
        </li>
        @if(Auth::check())
        <li>
            <a href="{{ route('profile.edit') }}">
                <span class="menu-item">Mi Perfil</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('betcontrol.index') }}">
                <span class="menu-item">wallet</span>
            </a>
        </li>
        <li>
            <a href="{{ route('draw-number-winner') }}">
                <span class="menu-item">Draw Number Winner</span>
            </a>
        </li>

        @if(in_array(Auth::user()->role_id, [1, 2]))
        <li>
            <a href="{{ route('game_configurations.index') }}">
                <span class="menu-item">Game Configurations</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('reports.index') }}">
                <span class="menu-item">Reports</span>
            </a>
        </li>
        @if(Auth::user()->role_id <> 4 )
        <li>
            <a href="{{ route('users.index') }}">
                <span class="menu-item">Users</span>
            </a>
        </li>
        @endif

        <li class="dropdown-item">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-button">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
        const container = document.querySelector('.page-container');
        container.classList.toggle('sidebar-active');
    }

    window.onclick = function(event) {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar.contains(event.target) && sidebar.classList.contains('active') && event.target !== document.getElementById('menuToggle')) {
            toggleMenu();
        }
    }
</script>
