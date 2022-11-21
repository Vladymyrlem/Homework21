<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\LabelController;
use App\Http\Controllers\API\AuthController;
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

/*Work*/
Route::get('/user/{user}/verify', [AuthController::class, 'verify'])->name('verify_email');

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

/*Work*/
Route::post('/users', [UserController::class, 'store'])->name('users.store');
/*Work*/
Route::put('/users', [UserController::class, 'update'])->name('users.update');
/*Work*/
Route::get('/users', [UserController::class, 'list'])->name('users.list');
/*Work*/
Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');




Route::middleware('auth:sanctum')->group(function (){
    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');
    /*Work*/
    Route::post('/users/projects', [ProjectController::class, 'store'])->name('projects.store');
    /*Work*/
    Route::post('/projects/link/users', [ProjectController::class, 'linkUsers'])->name('projects.link-users');
    /*Work*/
    Route::get('/projects', [ProjectController::class, 'list'])->name('projects.list');
    /*Work*/
    Route::delete('/projects', [ProjectController::class, 'destroy'])->name('projects.destroy');
    /*Work*/
    Route::post('/users/labels', [LabelController::class, 'store'])->name('labels.store');
    /*Work*/
    Route::post('/labels/link/projects', [LabelController::class, 'linkUsers'])->name('labels.link-projects');
    /*Work*/
    Route::get('/labels', [LabelController::class, 'list'])->name('labels.list');
    /*Work*/
    Route::delete('/labels', [LabelController::class, 'destroy'])->name('labels.destroy');
});



