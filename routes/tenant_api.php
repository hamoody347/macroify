<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobFunctionController;
use App\Http\Controllers\SOPController;

use App\Http\Controllers\Auth\LoginController;
/*
|--------------------------------------------------------------------------
| Tenant API Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant api routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Auth Routes
    Route::post('/login', [LoginController::class, 'login'])->name('users.login');
    Route::middleware(['auth:sanctum'])->get('/logout', [LoginController::class, 'logout'])->name('users.logout');

    Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        // User Routes
        Route::get('/users', [UserController::class, 'index'])->name('index.users'); // Get all users
        Route::get('/users/data', [UserController::class, 'data'])->name('data.users'); // Get population data for view
        Route::get('/users/{id}', [UserController::class, 'show'])->name('show.users'); // Get a single user
        Route::post('/users', [UserController::class, 'store'])->name('store.users'); // Create a new user
        Route::put('/users/{id}', [UserController::class, 'update'])->name('update.users'); // Update an existing user

        // Category Routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('index.categories'); // Get all categories
        Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('show.categories'); // Get a single category
        Route::post('/categories', [CategoryController::class, 'store'])->name('store.categories'); // Create a new category
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('update.categories'); // Update an existing category
        // Route::get('/categories/data', [CategoryController::class, 'data'])->name('data.categories'); // Get population data for view

        // Department Routes
        Route::get('/departments', [DepartmentController::class, 'index'])->name('index.departments'); // Get all departments
        Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('show.departments'); // Get a single department
        Route::post('/departments', [DepartmentController::class, 'store'])->name('store.departments'); // Create a new department
        Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('update.departments'); // Update an existing department
        // Route::get('/departments/data', [DepartmentController::class, 'data'])->name('data.departments'); // Get population data for view

        // JobFunction Routes
        Route::get('/job-functions', [JobFunctionController::class, 'index'])->name('index.jobfunctions'); // Get all job functions
        Route::get('/job-functions/data', [JobFunctionController::class, 'data'])->name('data.jobfunctions'); // Get population data for view
        Route::get('/job-functions/{id}', [JobFunctionController::class, 'show'])->name('show.jobfunctions'); // Get a single job function
        Route::post('/job-functions', [JobFunctionController::class, 'store'])->name('store.jobfunctions'); // Create a new job function
        Route::put('/job-functions/{id}', [JobFunctionController::class, 'update'])->name('update.jobfunctions'); // Update an existing job function

        // SOP Routes
        Route::get('/sops', [SOPController::class, 'index'])->name('index.sops'); // Get all sops
        Route::get('/sops/data', [SOPController::class, 'data'])->name('data.sops'); // Get population data for view
        Route::get('/sops/{id}', [SOPController::class, 'show'])->name('show.sops'); // Get a single sop
        Route::post('/sops', [SOPController::class, 'store'])->name('store.sops'); // Create a new SOP
        Route::put('/sops/{id}', [SOPController::class, 'update'])->name('update.sops'); // Update an existing SOP
    });
});
