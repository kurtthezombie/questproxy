<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\TransactionHistory;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        TransactionHistory::create([
            'payment_id' => $payment->id,
            'status' => "pending",
        ]);
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        TransactionHistory::create([
            'payment_id' => $payment->id,
            'status' => $payment->status,
        ]);
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
