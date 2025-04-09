<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\TransactionHistory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    public function transactionByUser($user_id) {
        $transactions = TransactionHistory::whereHas('payment', function ($query) use ($user_id) {
            $query->where('payer_id', $user_id);
        })->get();

        if($transactions->isEmpty()){
            return $this->successResponse('No transactions found for this user.',204);
        }

        return $this->successResponse('Transactions retrieved.',200,['transactions' => $transactions]);
    }

    public function transactionByPayment($payment_id){
        $transactions = TransactionHistory::where('payment_id',$payment_id)->get();

        return $this->successResponse('Transactions by payment retrieved.',200,['transactions' => $transactions]);
    }

    public function exportTransactionHistory($user_id){
        return Excel::download(new TransactionsExport($user_id), 'transaction_history.csv');
    }
}
