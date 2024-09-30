<?php
//controllers
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\GamerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//grouped routes that use middleware laravel sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(LoginController::class)->group(function () {
        //logout
        Route::post('logout',[LoginController::class,'logout']);
        Route::get('test',[LoginController::class,'testAuth']);
    });

    Route::controller(UserController::class)->group(function () {
        //delete user
        Route::delete('/delete/user/{id}', 'destroy');
        Route::get('check_login', 'checklogin');
        Route::get('/users/edit/{id}', 'edit');
        Route::patch('/users/edit/{id}', 'update');
    });

    Route::controller(PilotController::class)->group(function () {
        //Add more routes that need to use the login authentication
        Route::get('pilot/edit{id}', 'edit');
        //patch because we're only updating some columns, not the whole record
        Route::patch('pilot/edit/{id}', 'update');
        //portfolio routes

        Route::post('portfolio/create', 'createPortfolio');
        //u cant take pilot id without user id
        //if u view profile, it should be based on the user_id
        //so this route needs drafting for now
        Route::get('portfolio/show/{id}', 'showPortfolio');
        //take portfolio id
        Route::patch('portfolio/edit/{id}', 'editPortfolio');
        //take portfolio id
        Route::delete('portfolio/destroy/{id}',[PilotController::class,'destroyPortfolio']);
    });

    Route::controller(GamerController::class)->group(function() {
        Route::get('gamers/edit/{id}','edit');
        Route::patch('gamers/edit/{id}','update');
    });

    Route::controller(ServiceController::class)->group(function () {
      Route::get('service/index','index');
      Route::get('service/{id}','show');
      Route::post('service/create','store');
      Route::get('service/edit/{id}','edit');
      Route::post('service/edit/{id}','update');
      Route::delete('service/destroy/{id}','destroy');
    });

    Route::controller(RankController::class)->group(function () {
      Route::get('leaderboards', 'index');
      Route::get('leaderboard/{id}','show');
    });
});


//login
Route::post('login', [LoginController::class,'login']);
//register
Route::post('signup', [UserController::class,'create']);

//should be middleware or not?
Route::get('user/{id}', [UserController::class,'show']);
Route::get('users', [UserController::class,'index']);


//CAPTCHA:
Route::controller(CaptchaController::class)->group(function () {
    //generate captcha
    //Route::get('load-catpcha','load');
    //Route::post('post-captcha', 'post'); 
});


//take pilot id
//Route::delete('portfolio/destroy/{id}',[PilotController::class,'destroyAllPortfolio']);
Route::post('rank/create', [RankController::class, 'store']);
Route::delete('rank/delete/{id}',[RankController::class, 'destroy']);

//testing
Route::get('testPostman', function() {
    return response()->json('Postman is works and is running',200);
});