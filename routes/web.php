<?php

use App\Http\Controllers\Admin\AppoitmentController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\SideBarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group( function () {

    // -------------------------  Administrator Side Bar Routes  ----------------------------------------------- //
    Route::get('/api/data/user', [SideBarController::class, 'index']);

    // -------------------------  Administrator User Management Routes  ----------------------------------------------- //
    Route::get('/api/users', [UsersController::class, 'index']);
    Route::post('/api/users', [UsersController::class, 'store']);
    Route::patch('/api/users/{user}/change-role', [UsersController::class, 'changeRole']);
    Route::put('/api/users/{user}', [UsersController::class, 'update']);
    Route::delete('/api/users/{user}', [UsersController::class, 'destroy']);
    Route::delete('/api/users', [UsersController::class, 'bulkDelete']);

    // -------------------------  Administrator Blogs Management Routes  ----------------------------------------------- //
    Route::get('/api/appointments', [AppoitmentController::class, 'index']);



    // -------------------------  Administrator Profile Management Routes  ----------------------------------------------- //
    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController::class, 'uploadImage']);
    Route::post('/api/change-password', [ProfileController::class, 'changePassword']);

    // -------------------------  Administrator Permissions Management Routes  ----------------------------------------------- //
    Route::get('/api/permissions', [PermissionsController::class, 'index']);
    Route::post('/api/permissions', [PermissionsController::class, 'store']);
    Route::put('/api/permissions/{permission}', [PermissionsController::class, 'update']);
    Route::delete('/api/permissions/{permission}', [PermissionsController::class, 'destroy']);
    Route::delete('/api/permissions', [PermissionsController::class, 'bulkDelete']);

    // -------------------------  Administrator Roles Management Routes  ----------------------------------------------- //
    Route::get('/api/roles', [RolesController::class, 'index']);
    Route::post('/api/roles', [RolesController::class, 'store']);
    Route::put('/api/roles/{role}', [RolesController::class, 'update']);
    Route::put('/api/roles-permissions/{role}', [RolesController::class, 'updateRolePermissions']);
    Route::delete('/api/roles/{role}', [RolesController::class, 'destroy']);
    Route::delete('/api/roles', [RolesController::class, 'bulkDelete']);



});

Route::get('{view}', ApplicationController::class)->where('view','(.*)')->middleware('auth');
