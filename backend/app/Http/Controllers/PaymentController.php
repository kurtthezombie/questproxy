<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use Auth;
use DB;
use Exception;
use Http;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $bookingService;
    protected $paymentService;

    public function __construct(BookingService $bookingService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payment = Payment::all();
        return $this->successResponse('Payment records retrieved.', 200, ['payment' => $payment]);
    }

    public function pay(Request $request, $booking_id)
    {
        try {
            // Get booking and service
            $booking = $this->bookingService->findById($booking_id);
            $service = $booking->service;

            // Create payment session
            $responseData = $this->paymentService->createPaymentSession($service, $request->success_url, $request->cancel_url);

            // Store payment record
            $this->paymentService->storePaymentRecord($responseData, $booking_id);

            return $this->successResponse(
                'Payment record created, redirect to checkout URL.',
                201,
                ['checkout_url' => $responseData['data']['attributes']['checkout_url']]
            );
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function success($transaction_id){
        try {
            $payment = $this->paymentService->findByTransactionId($transaction_id);
            $paymentData = $this->paymentService->fetchPaymentStatus($transaction_id);

            $this->paymentService->updatePaymentStatus($payment, $paymentData['data']['attributes']);

            return $this->successResponse('Payment successful.', 200);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }
    }

    public function paymentsPaid($user_id)
    {
        $payments = $this->paymentService->getPaidPaymentByUserId($user_id);

        if ($payments->isEmpty()) {
            return $this->successResponse('No paid payments found for this user.', 204);
        }

        return $this->successResponse('Payments retrieved.', 200, ['payments' => $payments]);
    }
}
