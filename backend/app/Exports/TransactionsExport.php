<?php

namespace App\Exports;

use App\Models\TransactionHistory;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionsExport implements FromCollection
{

    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return TransactionHistory::whereHas('payment', function ($query) {
            $query->where('payer_id', $this->user_id);
        })
            ->with('payment')
            ->get()
            ->map(function ($transaction) {
                return [
                    'transaction_id' => $transaction->id,
                    'payment_amount' => $transaction->payment->amount,
                    'payment_method' => $transaction->payment->method,
                    'transaction_status' => $transaction->status,
                    'payer_id' => $transaction->payment->payer_id,
                    'booking_id' => $transaction->payment->booking_id,
                ];
            });
    }

    public function headings() {
        return [
            'Transaction ID',
            'Payment Amount',
            'Payment Method',
            'Transaction Status',
            'Payer ID',
            'Booking ID',
        ];
    }
}
