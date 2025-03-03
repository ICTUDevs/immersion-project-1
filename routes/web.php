<?php

use App\Events\RefreshUser;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\AttendanceController;


Route::post('/', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Route::get('/updates', function () {
    //     RefreshUser::dispatch(request()->msg);

    //     return 'done';
    // });

    Route::prefix('system')
        ->name('system.')
        ->controller(SystemController::class)
        ->group(fn() => [
            Route::middleware('isAdmin')->group(fn() => [
                Route::get('user', 'user')->name('user'),
                Route::get('user/{hashedId}', 'editUser')->name('user.edit'),
                Route::put('user/update/{hashedId}', 'updateUser')->name('user.update'),
            ]),

            Route::middleware('isSuperadmin')->group(fn() => [

                Route::get('create', 'createUser')->name('user.create'),
                Route::post('user', 'storeUser')->name('user.store'),
                Route::delete('user/{hashedId}', 'destroyUser')->name('user.delete'),


                Route::get('permission', 'permission')->name('permission'),
                Route::get('permission/create', 'createPermission')->name('permission.create'),
                Route::get('permission/edit/{hashedId}', 'editPermission')->name('permission.edit'),

                Route::post('permission', 'storePermission')->name('permission.store'),
                Route::delete('permission/{hashedId}', 'destroyPermission')->name('permission.delete'),
                Route::put('permission/{hashedId}', 'updatePermission')->name('permission.update'),

                Route::get('role', 'role')->name('role'),
                Route::get('role/create', 'createRole')->name('role.create'),
                Route::get('role/edit/{hashedId}', 'editRole')->name('role.edit'),
                Route::get('role/permission/{hashedId}', 'viewPermission')->name('role.permission'),

                Route::post('role', 'storeRole')->name('role.store'),

                Route::put('role/{hashedId}', 'assignPermission')->name('role.permission.update'),


                Route::delete('role/{hashedId}', 'destroyRole')->name('role.delete'),

                Route::get('batchprint', 'batchPrint')->name('batchprint'),
            ]),

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
            Route::get('countUsersWithTimeIn', 'countUsersWithTimeIn')->name('countUsersWithTimeIn'),
            Route::get('profile/{hashedId}', 'profile')->name('profile'),
            Route::get('manage/log/{hashedId}', 'editProfile')->name('log.edit'),
            Route::get('fetchQRCode', 'fetchQrcode')->name('fetchQrcode'),

            Route::get('create', 'create')->name('create'),
            Route::get('edit/{hashedId}', 'edit')->name('edit'),

            Route::post('store', 'store')->name('store'),

            Route::put('update/{hashedId}', 'update')->name('update'),
            Route::put('log/update/{hashedId}/{hashed_id}', 'updateLog')->name('log.update'),

            Route::delete('delete/{hashedId}/{hashed_id}', 'destroy')->name('delete'),
        ]);

    Route::controller(AttendanceController::class)->group(fn() => [
        Route::get('/dashboard', 'dashboard')->name('dashboard'),
    ]);
});

require_once __DIR__ . '/fortify.php';
