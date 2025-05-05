<?php
//controllers
use App\Events\NotificationBroadcastEvent;
use App\Events\TestEvent;
use App\Exports\TransactionsExport;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GamerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PilotController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;


use App\Mail\EmailVerification;
use App\Models\User;
use App\Notifications\PilotMatchedNotification;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route for searching users 
Route::get('/users/search', [UserController::class, 'search']);

//verify-email
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

//grouped routes that use middleware laravel sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/check-auth', function (Request $request) {
        return response()->json([
            'message' => 'Authenticated user found',
            'user' => $request->user(),
            'auth_user' => Auth::user()
        ]);
    });

    //email verification
    Route::controller(EmailVerificationController::class)->group(function () {
        Route::post('otp/check','checkOtp');
        Route::post('otp/resend','resend');
        Route::post('otp/cancel','cancel');
    });

    Route::controller(LoginController::class)->group(function () {
        //logout
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('test', [LoginController::class, 'testAuth']);
    });

    Route::controller(UserController::class)->group(function () {
        //delete user
        Route::get('users', 'index');
        Route::get('users/{id}', 'show');
        Route::get('users/username/{username}', 'showByUsername');
        Route::post('users/delete-email', 'requestAccountDeletion');
        Route::delete('users/delete/{id}', 'destroy');
        Route::get('check_login', 'checklogin'); //just for checking
        Route::get('users/edit/{id}', 'edit');
        Route::patch('users/edit/{id}', 'update');
        Route::get('users/search', 'search');
    });

    Route::controller(PilotController::class)->group(function () {
        Route::get('pilots', 'index');
        Route::get('pilots/{id}', 'show');
        Route::get('pilots/edit/{id}', 'edit');
        //patch because we're only updating some columns, not the whole record
        Route::patch('pilots/edit/{id}', 'update');

    });

    Route::controller(PortfolioController::class)->group(function () {
        Route::get('portfolios/{id}', 'show');//
        Route::post('portfolios/create', 'store');//
        Route::get('portfolios/edit/{id}', 'edit');
        Route::put('portfolios/edit/{id}', 'update');
        Route::delete('portfolios/delete/{id}', 'destroy');
        Route::delete('portfolios/delete/pilot/{pilot_id}', 'destroyAll');
        Route::get('portfolios/user/{user_id}', 'getPortfolioByUser');
        Route::get('portfolios/user/points/{username}', 'getPointsByUsername');
    });

    Route::controller(GamerController::class)->group(function () {
        Route::get('gamers/edit/{id}', 'edit');
        Route::patch('gamers/edit/{id}', 'update');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('services', 'index');
        Route::get('services/search', 'search');
        Route::get('services/{id}', 'show');
        Route::post('services/create', 'store');
        Route::get('services/edit/{id}', 'edit');
        Route::patch('services/edit/{id}', 'update');
        Route::delete('services/destroy/{id}', 'destroy');
        Route::get('pilots/{pilot_id}/services','getServicesByPilot');
        Route::get('services/{id}/details', 'getServiceDetails');
    });

    Route::controller(ReportController::class)->group(function () {
        Route::get('reports','index');
        Route::post('reports','store');
        Route::get('reports/{id}','show');
    });

    Route::controller(BookingController::class)->group(function() {
        Route::get('bookings/my-bookings', 'booksByMe');
        Route::get('bookings/instructions/{booking_id}', 'getBookingInstructions');
        Route::get('bookings/{booking_id}', 'show');
        Route::post('bookings/store', 'store');
        Route::delete('bookings/{booking_id}', 'delete');
        Route::put('bookings/{booking_id}/status', 'markAsCompleted');
        Route::put('bookings/{booking_id}/instruction', 'updateInstruction');
        Route::get('bookings/service/{service_id}', 'booksByService');
        Route::get('bookings/client/{client_id}', 'booksByClient');
        Route::get('/pilot/bookings', 'getBookingByPilot');
        Route::put('bookings/{booking_id}/progress', 'updateProgress');
        Route::get('bookings/{booking_id}/with-relations', 'getBookingWithRelations');
    });

    Route::controller(ProgressController::class)->group(function() {
        Route::get('progress/booking/{booking_id}', 'getProgressByBooking');
        Route::post('progress', 'create');
        Route::delete('progress/{id}', 'delete');
    });

    Route::prefix('preferences')->controller(PreferenceController::class)->group(function () {
        Route::post('/', 'create');
        Route::get('/{user_id}', 'getUserPreference');
        Route::put('/', 'update');
    });

    Route::controller(PaymentController::class)->group(function() {
        Route::get('payments', 'index');
        Route::post('payments/{booking_id}', 'pay');
        Route::get('payments/success/{id}', 'success');
        Route::get('users/{user_id}/payments/paid', 'paymentsPaid');
        Route::get('payments/paid/export', 'exportPaidPayments');
    });

    Route::controller(TransactionController::class)->group(function() {
        Route::get('/transactions', 'transactionByUser');
        Route::get('/payments/{payment_id}/transactions','transactionByPayment');
        Route::get('/export-transaction-history', 'exportTransactionHistory');
    });

    Route::controller(NotificationController::class)->group(function() {
        Route::get('notifications', 'index');
        Route::post('notifications/{id}/mark-as-read', 'markAsRead');
        Route::post('notifications/mark-all-as-read', 'markAllAsRead');
    });

    Route::controller(ReviewController::class)->group(function() {
        Route::get('reviews/{id}/info', 'fetchServiceInfo');
        Route::post('reviews', 'store');
        Route::get('pilots/{pilotId}/reviews', 'indexByPilot');
    });

    Route::post('match-pilot',[MatchingController::class,'matchPilot']);
});

//login
Route::post('login', [LoginController::class, 'login']);
//register
Route::post('signup', [UserController::class, 'create']);

//Route::delete('portfolio/destroy/{id}',[PilotController::class,'destroyAllPortfolio']);
Route::controller(RankController::class)->group(function () {
    Route::get('leaderboards', 'index');
    Route::get('leaderboards/{id}', 'show');
});

//testing
Route::get('testPostman', function () {
    return response()->json('Postman is works and is running', 200);
});
Route::get('/test-export', function () {
    return Excel::download(new TransactionsExport(1), 'transactions.xlsx');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'index');
    Route::get('categories/{id}', 'show');
});

Route::get('test-email', function () {
    Mail::to('hello@gmail.com')->send(new EmailVerification('123456'));

    return response()->json(['message'=>'Okay sent'],201);
});

Route::get('/test-notification', function () {
    $user = User::find(1); // Replace with the actual user ID for testing
    $user->notify(new PilotMatchedNotification($user));
    event(new NotificationBroadcastEvent($user));
    return response()->json(['message' => 'Notification sent']);
});

