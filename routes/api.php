<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperUserController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth Routes
Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['auth:sanctum'])->get('/logout', [LoginController::class, 'logout']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // User Routes
    Route::get('/users', [SuperUserController::class, 'index']); // Get all users
    Route::get('/users/{id}', [SuperUserController::class, 'show']); // Get a single user
    Route::post('/users', [SuperUserController::class, 'store']); // Create a new user
    Route::put('/users/{id}', [SuperUserController::class, 'update']); // Update an existing user
    Route::delete('/users/{id}', [SuperUserController::class, 'delete']); // Update an existing user

    // Tenant Routes
    Route::get('/tenants', [TenantController::class, 'index']); // Get all tenants
    Route::get('/tenants/data', [TenantController::class, 'data']); // Get population data for view
    Route::get('/tenants/{id}', [TenantController::class, 'show']); // Get a single user
    Route::post('/tenants', [TenantController::class, 'store']); // Create a new user
    Route::put('/tenants/{id}', [TenantController::class, 'update']); // Update an existing user
    Route::delete('/tenants/{id}', [TenantController::class, 'delete']); // Update an existing user
});
