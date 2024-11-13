<?php

use App\Http\Controllers\AttendanceController;
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

    Route::prefix('system')
        ->name('system.')
        ->controller(SystemController::class)
        ->group(fn() => [
            Route::get('user', 'user')->name('user'),
            Route::get('user/{hashedId}', 'editUser')->name('user.edit'),


            Route::get('permission', 'permission')->name('permission'),
            Route::get('permission/create', 'createPermission')->name('permission.create'),
            Route::get('permission/edit/{hashedId}', 'editPermission')->name('permission.edit'),

            Route::get('role', 'role')->name('role'),
            Route::get('role/create', 'createRole')->name('role.create'),
            Route::get('role/edit/{hashedId}', 'editRole')->name('role.edit'),
            Route::get('role/permission/{hashedId}', 'viewPermission')->name('role.permission'),

            Route::post('permission', 'storePermission')->name('permission.store'),
            Route::post('role', 'storeRole')->name('role.store'),

            Route::put('role/{hashedId}', 'assignPermission')->name('role.permission.update'),
            Route::put('permission/{hashedId}', 'updatePermission')->name('permission.update'),
            Route::put('user/update/{hashedId}', 'updateUser')->name('user.update'),
        ]);

    Route::prefix('attendance')
        ->name('attendance.')
        ->controller(AttendanceController::class)
        ->group(fn() => [

            Route::get('index', 'index')->name('index'),
            Route::get('scanner', 'scanner')->name('scanner'),


            Route::get('create', 'create')->name('create'),
            Route::get('edit/{hashedId}', 'edit')->name('edit'),

            Route::post('store', 'store')->name('store'),

            Route::put('update/{hashedId}', 'update')->name('update'),

            Route::delete('delete/{hashedId}', 'destroy')->name('delete'),
        ]);

    Route::controller(AttendanceController::class)->group(fn() => [
        Route::get('/', 'dashboard')->name('dashboard'),
    ]);
});
