<?php

use App\Http\Controllers\Auth\SsoCallbackController;
use App\Http\Controllers\Auth\SsoRedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SmapController;
use App\Http\Controllers\TopRiskController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriRisikoController;
use Illuminate\Support\Facades\Route;

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

    // Unit Kerja
    Route::get('/unit-kerja', [UnitKerjaController::class, 'index'])
        ->middleware('permission:unit-kerja.view')
        ->name('unit-kerja.index');
    Route::get('/unit-kerja/create', [UnitKerjaController::class, 'create'])
        ->middleware('permission:unit-kerja.create')
        ->name('unit-kerja.create');
    Route::post('/unit-kerja', [UnitKerjaController::class, 'store'])
        ->middleware('permission:unit-kerja.create')
        ->name('unit-kerja.store');
    Route::get('/unit-kerja/{unitKerja}/edit', [UnitKerjaController::class, 'edit'])
        ->middleware('permission:unit-kerja.edit')
        ->name('unit-kerja.edit');
    Route::put('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'update'])
        ->middleware('permission:unit-kerja.edit')
        ->name('unit-kerja.update');
    Route::delete('/unit-kerja/{unitKerja}', [UnitKerjaController::class, 'destroy'])
        ->middleware('permission:unit-kerja.delete')
        ->name('unit-kerja.destroy');

    // HATI HATI ROUTE GALAK
    // JANGAN DIHAPUS
    // TOP RISK
    Route::get('/top-risk', [TopRiskController::class, 'index'])
        ->middleware('permission:toprisk.view')
        ->name('top-risk.index');
    Route::get('/top-risk/create', [TopRiskController::class, 'create'])
        ->middleware('permission:toprisk.create')
        ->name('top-risk.create');
    Route::post('/top-risk', [TopRiskController::class, 'store'])
        ->middleware('permission:toprisk.create')
        ->name('top-risk.store');
    Route::get('/top-risk/{topRisk}', [TopRiskController::class, 'show'])
        ->middleware('permission:toprisk.view')
        ->name('top-risk.show');
    Route::get('/top-risk/{topRisk}/edit', [TopRiskController::class, 'edit'])
        ->middleware('permission:toprisk.edit')
        ->name('top-risk.edit');
    Route::put('/top-risk/{topRisk}', [TopRiskController::class, 'update'])
        ->middleware('permission:toprisk.edit')
        ->name('top-risk.update');
    Route::delete('/top-risk/{topRisk}', [TopRiskController::class, 'destroy'])
        ->middleware('permission:toprisk.delete')
        ->name('top-risk.destroy');
    Route::post('/top-risk/{topRisk}/monitoring', [TopRiskController::class, 'storeMonitoring'])
        ->middleware('permission:toprisk.create')
        ->name('top-risk.monitoring.store');
    Route::put('/top-risk/{topRisk}/monitoring/{monitoring}', [TopRiskController::class, 'updateMonitoring'])
        ->middleware('permission:toprisk.edit')
        ->name('top-risk.monitoring.update');
    Route::delete('/top-risk/{topRisk}/monitoring/{monitoring}', [TopRiskController::class, 'destroyMonitoring'])
        ->middleware('permission:toprisk.delete')
        ->name('top-risk.monitoring.destroy');

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

    // Permissions Management
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

    // Risk Register - Department Risk
    Route::get('/risks/department', [DepartemenController::class, 'index'])
        ->middleware('permission:risk.view')
        ->name('department-risk.index');
    Route::get('/risks/department/create', [DepartemenController::class, 'create'])
        ->middleware('permission:risk.create')
        ->name('department-risk.create');
    Route::post('/risks/department', [DepartemenController::class, 'store'])
        ->middleware('permission:risk.create')
        ->name('department-risk.store');
    Route::get('/risks/department/{id}', [DepartemenController::class, 'show'])
        ->middleware('permission:risk.view')
        ->name('department-risk.show');
    Route::get('/risks/department/{id}/edit', [DepartemenController::class, 'edit'])
        ->middleware('permission:risk.edit')
        ->name('department-risk.edit');
    Route::put('/risks/department/{id}', [DepartemenController::class, 'update'])
        ->middleware('permission:risk.edit')
        ->name('department-risk.update');
    Route::delete('/risks/department/{id}', [DepartemenController::class, 'destroy'])
        ->middleware('permission:risk.delete')
        ->name('department-risk.destroy');

    // Risk Register - SMAP Risk
    Route::get('/risks/smap', [SmapController::class, 'index'])
        ->middleware('permission:risk.view')
        ->name('smap-risk.index');
    Route::get('/risks/smap/create', [SmapController::class, 'create'])
        ->middleware('permission:risk.create')
        ->name('smap-risk.create');
    Route::post('/risks/smap', [SmapController::class, 'store'])
        ->middleware('permission:risk.create')
        ->name('smap-risk.store');
    Route::get('/risks/smap/{id}', [SmapController::class, 'show'])
        ->middleware('permission:risk.view')
        ->name('smap-risk.show');
    Route::get('/risks/smap/{id}/edit', [SmapController::class, 'edit'])
        ->middleware('permission:risk.edit')
        ->name('smap-risk.edit');
    Route::put('/risks/smap/{id}', [SmapController::class, 'update'])
        ->middleware('permission:risk.edit')
        ->name('smap-risk.update');
    Route::delete('/risks/smap/{id}', [SmapController::class, 'destroy'])
        ->middleware('permission:risk.delete')
        ->name('smap-risk.destroy');

    //Kategori
    Route::get('/kategori-risiko', [KategoriRisikoController::class, 'index'])
        ->middleware('permission:kategori-risiko.view') // Kembali ke kategori-risiko
        ->name('kategori-risiko.index');
    Route::get('/kategori-risiko/create', [KategoriRisikoController::class, 'create'])
        ->middleware('permission:kategori-risiko.create')
        ->name('kategori-risiko.create');
    Route::post('/kategori-risiko', [KategoriRisikoController::class, 'store'])
        ->middleware('permission:kategori-risiko.create')
        ->name('kategori-risiko.store');
    Route::get('/kategori-risiko/{id}', [KategoriRisikoController::class, 'show'])
        ->middleware('permission:kategori-risiko.view')
        ->name('kategori-risiko.show');
    Route::get('/kategori-risiko/{id}/edit', [KategoriRisikoController::class, 'edit'])
        ->middleware('permission:kategori-risiko.edit')
        ->name('kategori-risiko.edit');
    Route::put('/kategori-risiko/{id}', [KategoriRisikoController::class, 'update'])
        ->middleware('permission:kategori-risiko.edit')
        ->name('kategori-risiko.update');
    Route::delete('/kategori-risiko/{id}', [KategoriRisikoController::class, 'destroy'])
        ->middleware('permission:kategori-risiko.delete')
        ->name('kategori-risiko.destroy');
        
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

require __DIR__.'/auth.php';
