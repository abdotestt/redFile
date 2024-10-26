<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;


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

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);





Route::middleware('auth:sanctum')->group(function () {
    // deconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
    // url de categories
    // Route::middleware('role:admin')->group(function () {
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::get('/categories/{id}', [CategoryController::class, 'show']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
            
            // users manage
            
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            // gestion des permissions 
            Route::post('/role/add-permission', [RolePermissionController::class, 'addPermissionToRole']);
            Route::post('/role/revoke-permission', [RolePermissionController::class, 'revokePermissionFromRole']);
            // 

            Route::prefix('roles')->group(function () {
                Route::get('/', [RoleController::class, 'index']); // Lister tous les rôles
                Route::post('/', [RoleController::class, 'store']); // Créer un nouveau rôle
                Route::get('/{id}', [RoleController::class, 'show']); // Afficher un rôle spécifique
                Route::put('/{id}', [RoleController::class, 'update']); // Mettre à jour un rôle spécifique
                Route::delete('/{id}', [RoleController::class, 'destroy']); // Supprimer un rôle spécifique
            });

            Route::prefix('permissions')->group(function () {
                Route::get('/', [PermissionController::class, 'index']); // Lister toutes les permissions
                Route::post('/', [PermissionController::class, 'store']); // Créer une nouvelle permission
                Route::get('/{id}', [PermissionController::class, 'show']); // Afficher une permission spécifique
                Route::put('/{id}', [PermissionController::class, 'update']); // Mettre à jour une permission spécifique
                Route::delete('/{id}', [PermissionController::class, 'destroy']); // Supprimer une permission spécifique
            });
    // });
//url de gestion des documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    // 


});




