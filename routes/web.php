<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InstallationRequestController;
use App\Http\Controllers\TowerDeviceController;
use App\Http\Controllers\TowerController;

use App\Http\Controllers\Admin\InstallationReviewController;
use App\Http\Controllers\Admin\CableController;
use App\Http\Controllers\Admin\UserControlController;

// ===== AUTH =====
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ===== VENDOR ROUTES =====
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/user', [InstallationRequestController::class, 'dashboard'])
        ->name('vendor.dashboard');

    Route::get('/user/request/create', [InstallationRequestController::class, 'create'])
        ->name('vendor.request.create');

    Route::post('/user/request', [InstallationRequestController::class, 'store'])
        ->name('vendor.request.store');

    Route::get('/user/requests', [InstallationRequestController::class, 'history'])
        ->name('vendor.requests.history');
});


// ===== ADMIN ROUTES =====
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', fn () => view('dashboard.admin'))
        ->name('admin.welcome');

    // --- Installations (review + export)
    Route::get('/installations', [InstallationReviewController::class, 'index'])
        ->name('admin.installations.index');

    Route::patch('/installations/{installation}', [InstallationReviewController::class, 'update'])
        ->name('admin.installations.update');

    Route::get('/installations/export', [InstallationReviewController::class, 'export'])
        ->name('admin.installations.export');

    // --- Devices
    Route::get('/devices', [TowerDeviceController::class, 'index'])
        ->name('devices.index');

    Route::post('/devices/stack-items', [TowerDeviceController::class, 'storeStackItem'])
        ->name('stack-items.store');

    Route::delete('/devices/stack-items/{stackItem}', [TowerDeviceController::class, 'deleteStackItem'])
        ->name('stack-items.delete');

    // --- Tower Images
    Route::get('/towers/{tower}/images', [TowerDeviceController::class, 'images'])
        ->name('towers.images');

    Route::post('/towers/{tower}/images', [TowerDeviceController::class, 'uploadImage'])
        ->name('towers.images.upload');

    // --- Towers CRUD
    Route::get('/towers', [TowerController::class, 'index'])->name('towers.index');
    Route::get('/towers/create', [TowerController::class, 'create'])->name('towers.create');
    Route::post('/towers', [TowerController::class, 'store'])->name('towers.store');
    Route::get('/towers/{tower}/edit', [TowerController::class, 'edit'])->name('towers.edit');
    Route::put('/towers/{tower}', [TowerController::class, 'update'])->name('towers.update');
    Route::delete('/towers/{tower}', [TowerController::class, 'destroy'])->name('towers.destroy');

    // --- Cables (KML + OTB/Port)
    Route::get('/cables', [CableController::class, 'index'])
        ->name('admin.cables.index');

    Route::post('/cables/kml', [CableController::class, 'uploadKml'])
        ->name('admin.cables.kml.upload');

    Route::get('/cables/towers/{tower}', [CableController::class, 'towerDetail'])
        ->name('admin.cables.tower.detail');

    Route::post('/cables/towers/{tower}/generate', [CableController::class, 'generateOtbPorts'])
        ->name('admin.cables.tower.generate');

    Route::patch('/cables/ports/{port}', [CableController::class, 'updatePort'])
        ->name('admin.cables.port.update');

    // --- User Control (Vendor)
    Route::get('/users', [UserControlController::class, 'index'])
        ->name('admin.users.index');

    Route::post('/users', [UserControlController::class, 'store'])
        ->name('admin.users.store');

    Route::patch('/users/{user}', [UserControlController::class, 'update'])
        ->name('admin.users.update');

    Route::patch('/users/{user}/status', [UserControlController::class, 'toggleStatus'])
        ->name('admin.users.toggleStatus');

    Route::patch('/users/{user}/reset-password', [UserControlController::class, 'resetPassword'])
        ->name('admin.users.resetPassword');

    Route::get('/users/export', [UserControlController::class, 'export'])
        ->name('admin.users.export');
});