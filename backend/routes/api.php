<?php
//controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//login
Route::post('login', [LoginController::class,'login']);
//logout
Route::post('logout',[LoginController::class,'logout']);
Route::post('signup', [UserController::class,'create']);
