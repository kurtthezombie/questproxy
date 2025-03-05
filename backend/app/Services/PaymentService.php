<?php

namespace App\Services;

use App\Models\Payment;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Http;

class PaymentService
{
      protected $secretKey;
      protected $payment;


      /**
       * PaymentService constructor.
       * @param Payment $payment
       */
      public function __construct(Payment $payment)
      {
            $this->secretKey = env('PAYMONGO_SECRET_KEY');
            $this->payment = $payment;
      }

      /**
       * Create payment session
       * @param $service
       * @param $successUrl
       * @param $cancelUrl
       * @return mixed
       * @throws Exception
       */
      public function createPaymentSession($service, $successUrl, $cancelUrl)
      {
            $amount = $service->price * 100;
            $description = $service->description;

            $payload = [
                  'data' => [
                        'attributes' => [
                              'send_email_receipt' => false,
                              'show_description' => false,
                              'show_line_items' => true,
                              'payment_method_types' => ["card", "gcash", "paymaya"],
                              'line_items' => [
                                    [
                                          'currency' => 'PHP',
                                          'amount' => $amount,
                                          'description' => $description,
                                          'quantity' => 1,
                                          'name' => 'Service'
                                    ]
                              ],
                              'success_url' => $successUrl,
                              'cancel_url' => $cancelUrl
                        ]
                  ]
            ];

            $response = Http::withHeaders($this->getHeaders())
                  ->withOptions([
                        'verify' => false,
                  ])
                  ->post('https://api.paymongo.com/v1/checkout_sessions', $payload);

            if (!$response->successful()) {
                  throw new Exception('Failed to create payment session.');
            }

            return $response->json();
      }

      /**
       * Store payment record
       * @param $responseData
       * @param $bookingId
       * @return Payment
       * @throws Exception
       */
      public function storePaymentRecord($responseData, $bookingId)
      {
            DB::beginTransaction();
            try {
                  // Store payment data in a variable for better readability
                  $paymentData = [
                        'amount' => $responseData['data']['attributes']['amount'],
                        'description' => $responseData['data']['attributes']['description'],
                        'transaction_id' => $responseData['data']['id'],
                        'payment_link' => $responseData['data']['attributes']['checkout_url'],
                        'status' => $responseData['data']['attributes']['status'],
                        'payer_id' => Auth::id(),
                        'booking_id' => $bookingId,
                  ];
                  $payment = $this->payment->create($paymentData);

                  DB::commit();
                  return $payment;
            } catch (Exception $e) {
                  DB::rollback();
                  throw new Exception('Failed to store payment: ' .  $e->getMessage());
            }
      }
      
      /**
       * get Headers for fetching api
       * @return array{Accept: string, Authorization: string, Content-Type: string}
       */
      private function getHeaders()
      {
            return [
                  'Accept' => 'application/json',
                  'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                  'Content-Type' => 'application/json',
            ];
      }


      /**
       * Get all payments
       * @return Payment[]|\Illuminate\Database\Eloquent\Collection
       */
      public function getAllPayments()
      {
            return $this->payment->all();
      }

      /**
       * Find payment by transaction id
       * @param $transaction_id
       * @return Payment
       * @throws Exception
       */
      public function findByTransactionId($transaction_id) {
            $payment = $this->payment->where('transaction_id', $transaction_id)->first();

            if (!$payment) {
                  throw new Exception('Transaction not found');
            }

            return $payment;
      }

      /**
       * Fetch payment status
       * @param $transaction_id which is the checkout session id
       * @return \Illuminate\Http\JsonResponse
       * @throws Exception
       */

      public function fetchPaymentStatus($transaction_id)
      {
            $url = "https://api.paymongo.com/v1/checkout_sessions/{$transaction_id}";
            $response = Http::withHeaders($this->getHeaders())->get($url);

            if(!$response->successful()) {
                  throw new Exception('Failed to fetch payment status.');
            }

            return $response->json();
      }

      /**
       * Update payment status
       * @param $payment
       * @param $data
       * @throws Exception
       */
      public function updatePaymentStatus($payment, $data)
      {
            if($data['status'] == 'paid') {
                  $payment->update([
                        'method' => $data['payment_method_used'],
                        'status' => 'paid'
                  ]);
            }
      }

      /**
       * Get paid payments by user id
       * @param $user_id
       * @return Payment[]|\Illuminate\Database\Eloquent\Collection
       * @throws Exception
       */
      public function getPaidPaymentByUserId($user_id)
      {
            return $this->payment->where('payer_id', $user_id)
                  ->where('status','paid')
                  ->get(); 
      }
}