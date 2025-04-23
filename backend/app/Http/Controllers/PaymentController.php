<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponseTrait;
use Auth;
use DB;
use Http;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $payment = Payment::all();
        return $this->successResponse('Payment records retrieved.', 200, ['payment' => $payment]);
    }

    public function pay(Request $request, $booking_id)
    {
        $success_url = $request->success_url;
        $cancel_url = $request->cancel_url;

        //get booking
        $booking = Booking::findOrFail($booking_id);
        if (!$booking) {
            return $this->failedResponse('No booking found.',404);
        }
        //get service
        $service = $booking->service;
        if (!$service) {
            return $this->failedResponse('No service retrieved.',404);
        }   
        //set amount to payment gateway format
        $amount = $service->price * 100;
        $description = $service->description;


        //call response
        $secret_key = env('PAYMONGO_SECRET_KEY');
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($secret_key . ':'),
            'Content-Type' => 'application/json'
        ])
        ->withOptions([
            'verify' => false,
        ])
        ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'send_email_receipt' => true,
                            'show_description' => true,
                            'show_line_items' => true,
                            'description' => $description,
                            'cancel_url' => $cancel_url,
                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' => $amount,
                                    'description' => $description,
                                    'name' => 'Service',
                                    'quantity' => 1
                                ]
                            ],
                            'payment_method_types' => [
                                "card","gcash","paymaya"
                            ],
                            'success_url' => $success_url,
                        ]
                    ]
                ]);

        //insert into payment db
        $responseData = $response->json();
        $checkout_id = $responseData['data']['id'];
        $checkout_url = $responseData['data']['attributes']['checkout_url'];
        $status = $responseData['data']['attributes']['status'];
        
        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'amount' => $amount/100,
                'description' => $description,
                'transaction_id' => $checkout_id,
                'payment_link' => $checkout_url,
                'status' => $status,
                'payer_id' => Auth::user()->id,
                'booking_id' => $booking_id,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage(),500);
        }
        //redirect to checkout_url
        //return redirect()->away($checkout_url);
        return $this->successResponse(
            'Payment record created, redirect to checkout url.',
            201,
            [
                'checkout_url'=> $checkout_url,
                'transaction_id' => $payment->transaction_id,
                ]
        );
    }

    public function success($transaction_id){
        //retrieve session check if paid
        $payment = Payment::where('transaction_id',$transaction_id)->first();
        
        if (!$payment){
            return $this->failedResponse('Transaction not found',404);
        }
        $checkout_session_id = $payment->transaction_id;
        $url = "https://api.paymongo.com/v1/checkout_sessions/{$checkout_session_id}";
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
            'Content-Type' => 'application/json'
        ])->get($url);
        $data = $response->json();

        $payment_method_used = $data['data']['attributes']['payment_method_used'];
        $status = $data['data']['attributes']['status'];
        
        if ($status == 'paid'){
            //update tables
            $payment->method = $payment_method_used;
            $payment->status = $status;
            $payment->save();

            //return response
            return $this->successResponse('Payment successful.',200);
        }
    }

    public function paymentsPaid($user_id){
        $payments = Payment::where('payer_id',$user_id)
                    ->where('status','paid')
                    ->get();
        if ($payments->isEmpty()){
            return $this->successResponse('No paid payments found for this user.',204);
        }

        return $this->successResponse('Payments retrieved.',200,['payments' => $payments]);       
    }
}
