<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Service;
use App\Models\TransactionHistory;
use App\Traits\ApiResponseTrait;
use Auth;
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

    public function pay($service_id)
    {
        //get service
        $service = Service::class($service_id);

        if (!$service) {
            return $this->failedResponse('No service found.',404);
        }

        //set amount to payment gateway format
        $amount = $service->price * 100;
        $description = $service->description;
        $success_url = env('APP_FRONTEND_URL') . "/payment/success";
        $cancel_url =  env('APP_FRONTEND_URL') . "/services/" .$service_id;

        //call response
        $secret_key = env('PAYMONGO_SECRET_KEY');
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($secret_key . ':'),
            'Content-Type' => 'application/json'
        ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'send_email_receipt' => false,
                            'show_description' => false,
                            'show_line_items' => true,
                            'payment_method_types' => [
                                "qrph","billease","card","dob","dob_ubp","brankas_bdo","brankas_landbank",
                                "brankas_metrobank","gcash","grab_pay","paymaya"
                            ],
                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' => $amount,
                                    'description' => $description,
                                    'quantity' => 1,
                                    'name' => 'Service'
                                ]
                            ],
                            'success_url' => $success_url,
                            'cancel_url' => $cancel_url
                        ]
                    ]
                ]);
        //insert into payment db
        $responseData = $response->json();
        $checkout_id = $responseData['data']['id'];
        $checkout_url = $responseData['data']['attributes']['checkout_url'];
        $status = $responseData['data']['attributes']['status'];

        $payment = Payment::create([
            'amount' => $amount,
            'description' => $description,
            'transaction_id' => $checkout_id,
            'payment_link' => $checkout_url,
            'status' => $status,
            'payer_id' => Auth::user()->id,
            'service_id' => $service_id,
        ]);
        //insert into transactions_history
        $this->addTransaction($payment->id, $status);
        //redirect to checkout_url
        return redirect()->away($checkout_url);
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
            $payment->save();
            
            //create transaction history
            $this->addTransaction($payment->id,$status);

            //return response
            return $this->successResponse('Payment successful.',200);
        }
    }

    public function addTransaction($payment_id, $status)
    {
        TransactionHistory::create([
            'payment_id' => $payment_id,
            'status' => $status,
        ]);
    }
}
