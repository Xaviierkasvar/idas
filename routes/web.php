<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\GameConfigurationsController;
use App\Http\Controllers\DrawNumberWinnerController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChiefController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BetControlController;

Route::post('/dologin', [LoginController::class, 'doLogin'])->name('login.dologin');
Route::get('/', [LoginController::class, 'login']);

// Middleware aplicado a todas las rutas que requieren autenticación y roles
Route::middleware(['auth'])->group(function () {

    // Rutas para el rol de Admin (ID: 1)
    Route::middleware('checkRole:1')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    });

    // Rutas para el rol de Chief (ID: 1,2)
    Route::middleware('checkRole:1,2')->group(function () {
        Route::get('/chief/dashboard', [ChiefController::class, 'index'])->name('chief.dashboard');
        Route::resource('game_configurations', GameConfigurationsController::class);
        Route::get('/draw-number-winner/create', [DrawNumberWinnerController::class, 'create'])->name('draw_number_winner.create');
        Route::post('/draw-number-winner/store', [DrawNumberWinnerController::class, 'store'])->name('draw_number_winner.store');
    });

    // Rutas para el rol de Leader (ID: 1,2,3)
    Route::middleware('checkRole:1,2,3')->group(function () {
        Route::get('/leader/dashboard', [LeaderController::class, 'index'])->name('leader.dashboard');

        // Rutas de gestión de usuarios
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::put('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    // Rutas accesibles a todos los roles autenticados
    Route::get('/seller/dashboard', [SellerController::class, 'index'])->name('seller.dashboard');
    Route::resource('bets', BetController::class);
    Route::get('/draw-number-winner', [DrawNumberWinnerController::class, 'index'])->name('draw-number-winner');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/filter', [ReportController::class, 'filter'])->name('reports.filter');
    Route::get('/betcontrol', [BetControlController::class, 'index'])->name('betcontrol.index');
    Route::get('/betcontrol/filter', [BetControlController::class, 'filter'])->name('betcontrol.filter');
    
    // Perfil de usuario
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');

    // Cierre de sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'index'])->name('logout');
});

// Ruta de inicio que redirige al dashboard correspondiente según el rol del usuario
Route::get('home', function() {
    $roleId = session('role_id');

    if ($roleId) {
        switch ($roleId) {
            case 1:
                return redirect()->route('admin.dashboard');
            case 2:
                return redirect()->route('chief.dashboard');
            case 3:
                return redirect()->route('leader.dashboard');
            case 4:
                return redirect()->route('seller.dashboard');
            default:
                return redirect('/');
        }
    }

    return redirect('/');
})->name('home');

