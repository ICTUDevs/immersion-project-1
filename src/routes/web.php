<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::controller(GoogleController::class)->group(fn() => [
    Route::get('/auth/google/redirect', 'handleGoogleRedirect')->name('auth.google.redirect'),
    Route::get('/auth/google/callback', 'handleGoogleCallback')
]);



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::middleware('isAdmin')->group(fn() => [
        Route::prefix('system')
            ->name('system.')
            ->controller(SystemController::class)
            ->group(fn() => [
                Route::get('user', 'user')->name('user'),
                Route::get('create', 'createUser')->name('user.create'),
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
                Route::post('user', 'storeUser')->name('user.store'),

                Route::put('role/{hashedId}', 'assignPermission')->name('role.permission.update'),
                Route::put('permission/{hashedId}', 'updatePermission')->name('permission.update'),
                Route::put('user/update/{hashedId}', 'updateUser')->name('user.update'),
            ])
    ]);


    Route::prefix('attendance')
        ->name('attendance.')
        ->controller(AttendanceController::class)
        ->group(fn() => [

            Route::middleware('isAdmin')->group(fn() => [
                Route::get('/', 'index')->name('index'),
            ]),
            Route::get('scanner', 'scanner')->name('scanner'),
            Route::get('fetchUser', 'fetchUser')->name('fetchUser'),
            Route::get('profile/{hashedId}', 'profile')->name('profile'),
            Route::get('manage/log/{hashedId}', 'editProfile')->name('log.edit'),

            Route::get('create', 'create')->name('create'),
            Route::get('edit/{hashedId}', 'edit')->name('edit'),

            Route::post('store', 'store')->name('store'),

            Route::put('update/{hashedId}', 'update')->name('update'),
            Route::put('log/update/{hashedId}/{hashed_id}', 'updateLog')->name('log.update'),

            Route::delete('delete/{hashedId}', 'destroy')->name('delete'),
        ]);

    Route::controller(AttendanceController::class)->group(fn() => [
        Route::get('/dashboard', 'dashboard')->name('dashboard'),
    ]);
});
