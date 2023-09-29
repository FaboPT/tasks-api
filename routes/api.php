<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('tasks')->group(function () {
    Route::get('', [TaskController::class, 'index'])->name('task.index');
    Route::post('', [TaskController::class, 'store'])->name('task.store');
    Route::put('/{id}', [TaskController::class, 'update'])->name('task.update')->middleware('permission.edit');
    Route::delete('/{id}', [TaskController::class, 'destroy'])->name('task.destroy')->middleware('permission.delete');
    Route::put('/set-performed/{id}', [TaskController::class, 'setPerformed'])->name('task.set_performed');

});
