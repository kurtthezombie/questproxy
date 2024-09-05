<?php
//controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\UserController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//grouped routes that use middleware laravel sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    //logout
    Route::post('logout',[LoginController::class,'logout']);
    //delete user
    Route::delete('/delete/user/{id}', [UserController::class, 'destroy']);
    Route::get('check_login', [UserController::class,'checklogin']);
    //Add more routes that need to use the login authentication

    Route::get('edit/pilot/{id}',[PilotController::class, 'edit']);
    //patch because we're only updating some columns, not the whole record
    Route::patch('edit/pilot/{id}',[PilotController::class, 'update']);
    
});

//login
Route::post('login', [LoginController::class,'login']);
//register
Route::post('signup', [UserController::class,'create']);

//should be middleware or not?
Route::get('user/{id}', [UserController::class,'show']);
Route::get('users', [UserController::class,'index']);