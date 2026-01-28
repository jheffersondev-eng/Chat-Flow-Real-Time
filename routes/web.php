<?php

use App\Http\Controllers\Auth\OAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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

// Broadcasting Authentication
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// OAuth Routes
Route::prefix('auth')->name('oauth.')->group(function () {
    Route::get('/{provider}/redirect', [OAuthController::class, 'redirect'])->name('redirect');
    Route::get('/{provider}/callback', [OAuthController::class, 'callback'])->name('callback');
});
