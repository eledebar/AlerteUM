<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Resolveur\ResolveurIncidentController;
use App\Http\Controllers\User\UserIncidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// UTILISATEUR
Route::middleware(['auth', 'role:utilisateur'])->prefix('utilisateur')->name('utilisateur.')->group(function () {
    Route::get('/home', function () {
        return view('utilisateur.home');
    })->name('home');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::get('/incidents/categories', function () {
        return view('utilisateur.incidents.categories');
    })->name('incidents.categories');

    Route::get('/incidents', [UserIncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [UserIncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [UserIncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}/edit', [UserIncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [UserIncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [UserIncidentController::class, 'destroy'])->name('incidents.destroy');
    Route::get('/incidents/{incident}', [UserIncidentController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/export/csv', [UserIncidentController::class, 'exportCsv'])->name('incidents.export.csv');
});

// RESOLVEUR
Route::middleware(['auth', 'role:resolveur'])->prefix('resolveur')->name('resolveur.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/incidents/export/csv', [ResolveurIncidentController::class, 'exportCsv'])->name('incidents.export.csv');
    Route::resource('incidents', ResolveurIncidentController::class);
});

// PROFIL & NOTIFS COMMUNS
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.exportCsv');
});

require __DIR__.'/auth.php';
