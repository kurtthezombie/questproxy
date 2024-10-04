<?php
//controllers
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\GamerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\PortfolioController;
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
        Route::get('users', 'index');
        Route::get('users/{id}','show');
        Route::delete('users/delete/{id}', 'destroy');
        Route::get('check_login', 'checklogin'); //just for checking 
        Route::get('users/edit/{id}', 'edit');
        Route::patch('users/edit/{id}', 'update');
    });

    Route::controller(PilotController::class)->group(function () {
        Route::get('pilots','index');
        Route::get('pilots/{id}','show');
        Route::get('pilots/edit/{id}', 'edit');
        //patch because we're only updating some columns, not the whole record
        Route::patch('pilots/edit/{id}', 'update');
        
    });

    Route::controller(PortfolioController::class)->group(function() {
        Route::get('portfolios/{id}','show');//
        Route::post('portfolios/create','store');//
        Route::get('portfolios/edit/{id}','edit');
        Route::patch('portfolios/edit/{id}','update');
        Route::delete('portfolios/delete/{id}','destroy');
        Route::delete('portfolios/delete/pilot/{pilot_id}', 'destroyAll');
    });

    Route::controller(GamerController::class)->group(function() {
        Route::get('gamers/edit/{id}','edit');
        Route::patch('gamers/edit/{id}','update');
    });

    Route::controller(ServiceController::class)->group(function () {
      Route::get('services','index');
      Route::get('services/{id}','show');
      Route::post('services/create','store');
      Route::get('services/edit/{id}','edit');
      Route::patch('services/edit/{id}','update');
      Route::delete('services/destroy/{id}','destroy');
    });

    //rank controllers here
});


//login
Route::post('login', [LoginController::class,'login']);
//register
Route::post('signup', [UserController::class,'create']);

//should be middleware or not?

//CAPTCHA:
Route::controller(CaptchaController::class)->group(function () {
    //generate captcha
    //Route::get('load-catpcha','load');
    //Route::post('post-captcha', 'post'); 
});


//take pilot id
//Route::delete('portfolio/destroy/{id}',[PilotController::class,'destroyAllPortfolio']);
Route::controller(RankController::class)->group(function () {
    Route::get('leaderboards', 'index');
    Route::get('leaderboard/{id}','show');
  });
Route::post('rank/create', [RankController::class, 'store']);
Route::delete('rank/delete/{id}',[RankController::class, 'destroy']);

//testing
Route::get('testPostman', function() {
    return response()->json('Postman is works and is running',200);
});