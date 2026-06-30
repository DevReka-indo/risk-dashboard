<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SsoCallbackController;
use App\Http\Controllers\Auth\SsoRedirectController;

Route::middleware('guest')->group(function (): void {
    Route::get('/sso/redirect', SsoRedirectController::class)->name('sso.redirect');
});

Route::get('/sso/callback', SsoCallbackController::class)->name('sso.callback');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/risks', function () {
        return 'Halaman Monitoring Risiko';
    })
        ->middleware('permission:risk.view')
        ->name('risks.index');

    // User Management
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:user.view')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('users.destroy');

    // Role Management
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:role.view')
        ->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('permission:role.create')
        ->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:role.create')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:role.edit')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:role.edit')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:role.delete')
        ->name('roles.destroy');

    //Permissions Management
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permission.view')
        ->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->middleware('permission:permission.create')
        ->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->middleware('permission:permission.create')
        ->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->middleware('permission:permission.edit')
        ->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->middleware('permission:permission.edit')
        ->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->middleware('permission:permission.delete')
        ->name('permissions.destroy');

    Route::get('/settings', function () {
        return 'Halaman Pengaturan';
    })
        ->middleware('permission:setting.view')
        ->name('settings.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
