<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Get authenticated user's details
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User registration
Route::post('/register', [AuthController::class, 'register']);

// User login
Route::post('/login', [AuthController::class, 'login']);

// User logout (requires authentication)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

use App\Http\Controllers\RolePermissionController;

Route::post('/role/add-permission', [RolePermissionController::class, 'addPermissionToRole']);
Route::post('/role/revoke-permission', [RolePermissionController::class, 'revokePermissionFromRole']);
Route::post('/role/sync-permissions', [RolePermissionController::class, 'syncPermissionsForRole']);

use App\Http\Controllers\CategoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});


use App\Http\Controllers\DocumentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
});
