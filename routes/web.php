<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\IncidentController as AdminIncidentController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Ruta pública
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas de utilisateur
Route::middleware(['auth', 'role:utilisateur'])->prefix('utilisateur')->name('utilisateur.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // ✅ RUTA DE CATEGORÍAS (DEBE IR ANTES DE LAS RUTAS CON {incident})
    Route::get('/incidents/categories', function () {
        return view('utilisateur.incidents.categories');
    })->name('incidents.categories');

    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
});

// Rutas de admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('incidents', AdminIncidentController::class);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

// Perfil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});

require __DIR__.'/auth.php';
