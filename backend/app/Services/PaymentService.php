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

      public function __construct(Payment $payment)
      {
            $this->secretKey = env('PAYMONGO_SECRET_KEY');
            $this->payment = $payment;
      }

      public function createPaymentSession($service, $successUrl, $cancelUrl)
      {
            $amount = $service->price * 100;
            $description = $service->description;

            $headers = [
                  'Accept' => 'application/json',
                  'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                  'Content-Type' => 'application/json',
            ];

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

            $response = Http::withHeaders($headers)
                  ->withOptions([
                        'verify' => false,
                  ])
                  ->post('https://api.paymongo.com/v1/checkout_sessions', $payload);

            if (!$response->successful()) {
                  throw new Exception('Failed to create payment session.');
            }

            return $response->json();
      }

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
                  throw new Exception('Failed to store payment: ', $e->getMessage());
            }
      }
      
}