<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/logout', [AuthController::class, 'logout'])->middleware('auth');


Route::get('/presence-request', [PresenceController::class, 'presenceRequest'])->middleware('auth');
Route::get('/pending', [PresenceController::class, 'pending'])->middleware('auth');
Route::get('/confirm/{presence}', [PresenceController::class, 'confirm'])->middleware('auth');
Route::get('/refuse/{presence}', [PresenceController::class, 'refuse'])->middleware('auth');
Route::get('/historic/{user}', [PresenceController::class, 'historic'])->middleware('auth');
Route::get('/presences', [PresenceController::class, 'presences'])->middleware('auth');
Route::get('/data-user', [PresenceController::class, 'dataUser'])->middleware('auth');




Route::apiResources(['users' => UserController::class]);



