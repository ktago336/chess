<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GetTopController;
use App\Http\Controllers\main;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [main::class, 'show']);
Route::get('/game/{id}', [\App\Http\Controllers\main::class, 'showGame']);
Route::post('/newgame', [\App\Http\Controllers\NewGameController::class, 'newGame']);
Route::post('/move', [\App\Http\Controllers\MoveController::class, 'move']);
//@todo give POST ROUTE
Route::get('/login', function (){
    return view('login');
});
Route::get('/logout', [\App\Http\Controllers\LoginController::class, 'logout']);

Route::get('/giveup', [\App\Http\Controllers\main::class, 'giveUp']);


Route::get('/register', function (){
    return view('register');
});

Route::post('/reg', [\App\Http\Controllers\RegisterController::class, 'reg']);
Route::get('/reg', function (){
    return back()->withErrors('error','error');
});

Route::post('/log', [\App\Http\Controllers\LoginController::class, 'login']);
Route::get('/log', function (){
    return back()->withErrors('error','error in login');
});
