<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/vehicles', [DashboardController::class, 'vehicles'])->name('dashboard.vehicles');
    Route::get('/dashboard/vehicles/{vehicle}', [DashboardController::class, 'showVehicle'])->name('dashboard.vehicles.show');
    // Web JSON search endpoint (uses external DB) via query params
    Route::get('/vehicles/search', [\App\Http\Controllers\Web\VehicleController::class, 'search'])->name('vehicles.search');
    // Tasks page (beautiful pastel design)
    Route::get('/dashboard/tasks', [DashboardController::class, 'tasks'])->name('dashboard.tasks');
    Route::get('/dashboard/users', [DashboardController::class, 'users'])->name('dashboard.users');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Schedule routes (now under Tasks menu)
    Route::get('/schedule', [\App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/fetch', [\App\Http\Controllers\ScheduleController::class, 'fetch'])->name('schedule.fetch');
    Route::get('/schedule/stats', [\App\Http\Controllers\ScheduleController::class, 'stats'])->name('schedule.stats');
    Route::get('/schedule/kanban', [\App\Http\Controllers\ScheduleController::class, 'kanban'])->name('schedule.kanban');
    // Developer utility: create a personal access token for current user
    Route::post('/tokens/create', [\App\Http\Controllers\ProfileController::class, 'createApiToken'])->name('tokens.create');
});

// Bridge: create web session from API token (used after REST login)
Route::post('/auth/token-login', [\App\Http\Controllers\AuthSessionController::class, 'tokenLogin'])->name('auth.token-login');
