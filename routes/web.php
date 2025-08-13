<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\IncidentDatatableController;

use App\Http\Controllers\User\UserIncidentController;
use App\Http\Controllers\Resolveur\ResolveurIncidentController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminIncidentController;
use App\Http\Controllers\Admin\AdminResolveurController;

use App\Http\Controllers\NotificationController;


Route::view('/', 'welcome')->name('home');

require __DIR__ . '/auth.php';


Route::middleware(['auth', 'verified'])->group(function () {

  
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/export.csv', [ExportController::class, 'incidentsCsv'])
        ->name('dashboard.export.csv');

 
    Route::prefix('utilisateur')->as('utilisateur.')->middleware('role:utilisateur')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/home',      [DashboardController::class, 'index'])->name('home');

        Route::view('/incidents/categories', 'utilisateur.incidents.categories')->name('incidents.categories');

        Route::view('/notifications', 'notifications.index')->name('notifications.index');

        Route::get('/incidents/datatable', [IncidentDatatableController::class, 'index'])
            ->name('incidents.datatable');

        Route::get('/incidents/export.csv', [UserIncidentController::class, 'exportCsv'])
            ->name('incidents.export.csv');

        Route::get   ('/incidents',                 [UserIncidentController::class, 'index'])->name('incidents.index');
        Route::get   ('/incidents/create',          [UserIncidentController::class, 'create'])->name('incidents.create');
        Route::post  ('/incidents',                 [UserIncidentController::class, 'store'])->name('incidents.store');
        Route::get   ('/incidents/{incident}',      [UserIncidentController::class, 'show'])->whereNumber('incident')->name('incidents.show');
        Route::get   ('/incidents/{incident}/edit', [UserIncidentController::class, 'edit'])->whereNumber('incident')->name('incidents.edit');
        Route::put   ('/incidents/{incident}',      [UserIncidentController::class, 'update'])->whereNumber('incident')->name('incidents.update');
        Route::delete('/incidents/{incident}',      [UserIncidentController::class, 'destroy'])->whereNumber('incident')->name('incidents.destroy');

        Route::post  ('/incidents/{incident}/confirm-close', [UserIncidentController::class, 'confirmClose'])
            ->whereNumber('incident')->name('incidents.confirmClose');
        Route::post  ('/incidents/{incident}/reject-close',  [UserIncidentController::class, 'rejectClose'])
            ->whereNumber('incident')->name('incidents.rejectClose');
    });


    Route::prefix('resolveur')->as('resolveur.')->middleware('role:resolveur')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/home',      [DashboardController::class, 'index'])->name('home');

        Route::view('/notifications', 'notifications.index')->name('notifications.index');

        Route::get('/incidents/datatable', [IncidentDatatableController::class, 'index'])
            ->name('incidents.datatable');

        Route::get('/incidents/export.csv', [ExportController::class, 'incidentsCsv'])
            ->name('incidents.export.csv');

        Route::get   ('/incidents',                 [ResolveurIncidentController::class, 'index'])->name('incidents.index');
        Route::get   ('/incidents/create',          [ResolveurIncidentController::class, 'create'])->name('incidents.create');
        Route::post  ('/incidents',                 [ResolveurIncidentController::class, 'store'])->name('incidents.store');
        Route::get   ('/incidents/{incident}',      [ResolveurIncidentController::class, 'show'])->whereNumber('incident')->name('incidents.show');
        Route::get   ('/incidents/{incident}/edit', [ResolveurIncidentController::class, 'edit'])->whereNumber('incident')->name('incidents.edit');
        Route::put   ('/incidents/{incident}',      [ResolveurIncidentController::class, 'update'])->whereNumber('incident')->name('incidents.update');
        Route::delete('/incidents/{incident}',      [ResolveurIncidentController::class, 'destroy'])->whereNumber('incident')->name('incidents.destroy');

        Route::post  ('/incidents/{incident}/take',     [ResolveurIncidentController::class, 'take'])->whereNumber('incident')->name('incidents.take');
        Route::post  ('/incidents/{incident}/status',   [ResolveurIncidentController::class, 'setStatus'])->whereNumber('incident')->name('incidents.status');
        Route::post  ('/incidents/{incident}/comment',  [ResolveurIncidentController::class, 'comment'])->whereNumber('incident')->name('incidents.comment');

        Route::post  ('/incidents/{incident}/priority', [ResolveurIncidentController::class, 'setPriority'])->whereNumber('incident')->name('incidents.priority');
        Route::post  ('/incidents/{incident}/escalate', [ResolveurIncidentController::class, 'escalate'])->whereNumber('incident')->name('incidents.escalate');
    });

  
    Route::prefix('admin')->as('admin.')->middleware('role:admin')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/incidents/datatable', [IncidentDatatableController::class, 'index'])
            ->name('incidents.datatable');

        Route::resource('resolveurs', AdminResolveurController::class)
            ->parameters(['resolveurs' => 'user'])
            ->except(['show']);

        Route::get   ('/incidents',                     [AdminIncidentController::class, 'index'])->name('incidents.index');
        Route::get   ('/incidents/{incident}',          [AdminIncidentController::class, 'show'])->whereNumber('incident')->name('incidents.show');
        Route::post  ('/incidents/{incident}/assign',   [AdminIncidentController::class, 'assign'])->whereNumber('incident')->name('incidents.assign');
        Route::post  ('/incidents/{incident}/status',   [AdminIncidentController::class, 'setStatus'])->whereNumber('incident')->name('incidents.status');
        Route::post  ('/incidents/{incident}/escalate', [AdminIncidentController::class, 'escalate'])->whereNumber('incident')->name('incidents.escalate');

        Route::get('/incidents/export.csv', [ExportController::class, 'incidentsCsv'])
            ->name('incidents.export');
    });

   
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index.global');
});
