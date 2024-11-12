<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SystemController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('system')
    ->name('system.')
    ->controller(SystemController::class)
    ->group(fn () => [
       Route::get('user', 'user')->name('user'),
       Route::get('permission', 'permission')->name('permission'),
       Route::get('permission/create', 'createPermission')->name('permission.create'),
       Route::get('permission/edit/{hashedId}', 'editPermission')->name('permission.edit'),

       Route::post('permission', 'storePermission')->name('permission.store'),

       Route::put('permission/{hashedId}', 'updatePermission')->name('permission.update'),
    ]);
});
