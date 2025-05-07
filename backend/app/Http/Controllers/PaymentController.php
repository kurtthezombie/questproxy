<?php

namespace App\Http\Controllers;

use App\Exports\PaymentsPaidExport;
use App\Models\Booking;
use App\Models\Payment;
use App\Traits\ApiResponseTrait;
use Auth;
use Carbon\Carbon;
use DB;
use Http;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\BookingConfirmedNotification;
use App\Models\Service;

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
        $cancel_url = $request->cancel_url;

        $booking = Booking::findOrFail($booking_id);
        $service = $booking->service;
        if (!$service) {
            return $this->failedResponse('No service retrieved.',404);
        }

        // Check for an approved negotiation with a final price
        $negotiation = $booking->negotiations()
            ->where('pilot_status', 'approved')
            ->orderByDesc('id')
            ->first();

        if ($negotiation && $negotiation->final_price) {
            $amount_pesos = $negotiation->final_price;
            $amount_centavos = $negotiation->final_price * 100;
            $description = $service->description . " (Negotiated)";
        } else {
            $amount_pesos = $service->price;
            $amount_centavos = $service->price * 100;
            $description = $service->description;
        }

        //payment initial creation
        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'amount' => $amount_pesos,
                'details' => $description,
                'payer_id' => Auth::user()->id,
                'booking_id' => $booking_id,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage(), 500);
        }

        //set success url
        //$success_url = 'BUTNGI UG ROUTE sa page nato after payment nya i append ang id sa payment';
        $success_url = env('APP_FRONTEND_URL') . '/verify-payment/' . $payment->id;

        //call <response></response>
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
                                    'amount' => $amount_centavos,
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

        //take response data
        $responseData = $response->json();
        
        if (!$response->successful() || !isset($responseData['data'])) {
            return $this->failedResponse('Payment gateway error or invalid response data.', 500);
        }

        //update payment record
        $payment->transaction_id = $responseData['data']['id'];
        $payment->payment_link = $responseData['data']['attributes']['checkout_url'];
        $payment->status = $responseData['data']['attributes']['status'];
        $payment->save();
        

        return $this->successResponse(
            'Payment record created, redirect to checkout url.',
            201,
            ['checkout_url'=> $payment->payment_link]
        );
    }

    public function success($id)
    {
        try {
            //retrieve session check if paid
            $payment = Payment::findOrFail($id);

            $checkout_session_id = $payment->transaction_id;
            $url = "https://api.paymongo.com/v1/checkout_sessions/{$checkout_session_id}";

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->get($url);

            $data = $response->json();

            if (!isset($data['data'])) {
                return $this->failedResponse('Invalid response from PayMongo.', 500);
            }

            $payment_method_used = $data['data']['attributes']['payment_method_used'];
            $status = $data['data']['attributes']['payments'][0]['attributes']['status'];

            if ($status == 'paid') {
                //update tables
                $payment->method = $payment_method_used;
                $payment->status = $status;
                $payment->save();

                // Get the booking and update its status
                $booking = $payment->booking;
                $booking->status = 'in_progress';
                $booking->save();

                // Send notification
                $this->sendBookingConfirmedNotification($booking);

                //return response
                return $this->successResponse('Payment successful.', 200, ['payment_status' => $status]);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->failedResponse($e->getMessage(), 500);
        }

        return $this->failedResponse('Payment not completed', 500);
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

    public function exportPaidPayments()
    {
        $user_id = Auth::user()->id;
        return Excel::download(new PaymentsPaidExport($user_id), 'payments_paid.xlsx');
    }

    private function getPilotUserFromBooking(Booking $booking)
    {
        return $booking->service->pilot->user;
    }

    private function getClientUserFromBooking(Booking $booking)
    {
        return $booking->client;
    }

    private function getServiceFromBooking(Booking $booking)
    {
        return $booking->service;
    }

    private function sendBookingConfirmedNotification(Booking $booking)
    {
        $pilotUser = $this->getPilotUserFromBooking($booking);
        $clientUser = $this->getClientUserFromBooking($booking);
        $service = $this->getServiceFromBooking($booking);
        
        $pilotUser->notify(new BookingConfirmedNotification($clientUser, $service, $booking));
    }
}
