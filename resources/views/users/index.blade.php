@extends('layouts.app')

@section('content')
<div class="page-container">
    <h2>User Management</h2>

    <!-- Filtro de Búsqueda y Selección de Rol -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by username, name, or last name">
        </div>
        <div class="col-md-4">
            <select id="roleFilter" class="form-select">
                <option value="">All Roles</option>
                @foreach($users->pluck('role.name')->unique() as $roleName)
                    <option value="{{ $roleName }}">{{ $roleName }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Create New Users</a>
    </div>

    <!-- Tabla de Usuarios -->
    <table class="table" id="usersTable">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->last_name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->role->name }}</td>
                    <td>
                        @if ($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('users.toggle', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Controles de paginación -->
    <div id="paginationControls" class="mt-4"></div>
</div>

<!-- JavaScript para el filtro y la paginación -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const usersTable = document.getElementById('usersTable').getElementsByTagName('tbody')[0];
    const rows = Array.from(usersTable.getElementsByTagName('tr'));
    const paginationControls = document.getElementById('paginationControls');
    const rowsPerPage = 5;
    let currentPage = 1;

    function filterTable() {
        const searchText = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;

        const filteredRows = rows.filter(row => {
            const cells = row.getElementsByTagName('td');
            const username = cells[0].textContent.toLowerCase();
            const name = cells[1].textContent.toLowerCase();
            const lastName = cells[2].textContent.toLowerCase();
            const role = cells[4].textContent;

            const matchesSearch = username.includes(searchText) || name.includes(searchText) || lastName.includes(searchText);
            const matchesRole = selectedRole === '' || role === selectedRole;

            return matchesSearch && matchesRole;
        });

        displayPage(filteredRows, currentPage);
        setupPagination(filteredRows);
    }

    function setupPagination(filteredRows) {
        paginationControls.innerHTML = '';
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.add('btn', 'btn-sm', 'btn-primary', 'me-1');
            if (i === currentPage) pageButton.classList.add('active');
            pageButton.addEventListener('click', function () {
                currentPage = i;
                displayPage(filteredRows, currentPage);
                setupPagination(filteredRows);
            });
            paginationControls.appendChild(pageButton);
        }
    }

    function displayPage(filteredRows, page) {
        usersTable.innerHTML = '';
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const rowsToShow = filteredRows.slice(start, end);

        rowsToShow.forEach(row => usersTable.appendChild(row));
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);

    filterTable();
});
</script>
@endsection
