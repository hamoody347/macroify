<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Http\Controllers\FAQController;
use App\Http\Controllers\SOPController;
use App\Http\Controllers\WikiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PolicyBookController;
use App\Http\Controllers\JobFunctionController;

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
    Route::get('/fallbacklogin', function () {
        return response()->json(["message" => "Not Authenticated!"]);
    })->name('login');

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

        // Department Routes
        Route::get('/departments', [DepartmentController::class, 'index'])->name('index.departments'); // Get all departments
        Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('show.departments'); // Get a single department
        Route::post('/departments', [DepartmentController::class, 'store'])->name('store.departments'); // Create a new department
        Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('update.departments'); // Update an existing department

        // JobFunction Routes
        Route::get('/job-functions', [JobFunctionController::class, 'index'])->name('index.jobfunctions'); // Get all job functions
        Route::get('/job-functions/data', [JobFunctionController::class, 'data'])->name('data.jobfunctions'); // Get population data for view
        Route::get('/job-functions/{id}', [JobFunctionController::class, 'show'])->name('show.jobfunctions'); // Get a single job function
        Route::post('/job-functions', [JobFunctionController::class, 'store'])->name('store.jobfunctions'); // Create a new job function
        Route::put('/job-functions/{id}', [JobFunctionController::class, 'update'])->name('update.jobfunctions'); // Update an existing job function

        // SOP Routes
        Route::get('/sops', [SOPController::class, 'index'])->name('index.sops'); // Get all SOPS
        Route::get('/sops/data', [SOPController::class, 'data'])->name('data.sops'); // Get population data for view
        Route::get('/sops/{id}', [SOPController::class, 'show'])->name('show.sops'); // Get a single SOP
        Route::post('/sops', [SOPController::class, 'store'])->name('store.sops'); // Create a new SOP
        Route::put('/sops/{id}', [SOPController::class, 'update'])->name('update.sops'); // Update an existing SOP
        Route::delete('/sops/{id}', [SOPController::class, 'delete'])->name('delete.sops'); // Update an existing SOP

        // FAQ Routes
        Route::get('/faqs', [FAQController::class, 'index'])->name('index.faqs'); // Get all FAQS
        Route::get('/faqs/data', [FAQController::class, 'data'])->name('data.faqs'); // Get population data for view
        Route::get('/faqs/{id}', [FAQController::class, 'show'])->name('show.faqs'); // Get a single FAQ
        Route::post('/faqs', [FAQController::class, 'store'])->name('store.faqs'); // Create a new FAQ
        Route::put('/faqs/{id}', [FAQController::class, 'update'])->name('update.faqs'); // Update an existing FAQ
        Route::delete('/faqs/{id}', [FAQController::class, 'delete'])->name('delete.faqs'); // Update an existing FAQ

        // Wiki Routes
        Route::get('/wikis', [WikiController::class, 'index'])->name('index.wikis'); // Get all WikiS
        Route::get('/wikis/data', [WikiController::class, 'data'])->name('data.wikis'); // Get population data for view
        Route::get('/wikis/{id}', [WikiController::class, 'show'])->name('show.wikis'); // Get a single Wiki
        Route::post('/wikis', [WikiController::class, 'store'])->name('store.wikis'); // Create a new Wiki
        Route::put('/wikis/{id}', [WikiController::class, 'update'])->name('update.wikis'); // Update an existing Wiki
        Route::delete('/wikis/{id}', [WikiController::class, 'delete'])->name('delete.wikis'); // Update an existing Wiki

        // Policy Routes
        Route::get('/policies', [PolicyBookController::class, 'index'])->name('index.policies'); // Get all policies
        Route::get('/policies/data', [PolicyBookController::class, 'data'])->name('data.policies'); // Get population data for view
        Route::get('/policies/assigned', [PolicyBookController::class, 'assigned'])->name('index.assigned.policies'); // Get population data for view
        Route::get('/policies/assigned/{id}', [PolicyBookController::class, 'showAssigned'])->name('assigned.policies'); // Get a single assigned policy
        Route::get('/policies/{id}', [PolicyBookController::class, 'show'])->name('show.policies'); // Get a single policy
        Route::post('/policies', [PolicyBookController::class, 'store'])->name('store.policies'); // Create a new policy
        Route::post('/policies/{id}/agreement', [PolicyBookController::class, 'agreement'])->name('agreement.policies'); // Mark Policy as agreed to
        Route::put('/policies/{id}', [PolicyBookController::class, 'update'])->name('update.policies'); // Update an existing policy
        Route::delete('/policies/{id}', [PolicyBookController::class, 'delete'])->name('delete.policies'); // Delete an existing policy

    });
});
