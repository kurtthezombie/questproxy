<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\TransactionHistory;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class TransactionController extends Controller
{
    use ApiResponseTrait;

    public function transactionByUser(Request $request) {
        //get logged in user id
        $user_id = $this->getUserId();

        $query = TransactionHistory::with('payment') // Eager load the payment relation
            ->whereHas('payment', function ($query) use ($user_id) {
                $query->where('payer_id', $user_id);
            });

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                        ->orWhere('status', 'LIKE', "%$search%")
                        ->orWhereHas('payment', function ($query) use ($search) {
                            $query->where('amount', 'LIKE', "%$search%");  // Searching amount in payment
                        });
            });
        }

        $transactions = $query->orderBy('updated_at', 'desc')->paginate(10);

        if ($transactions->isEmpty()) {
            return $this->successResponse('No transactions found for this user.', 200, ['user_id' => $user_id]);
        }
        
        return $this->successResponse('Transactions retrieved.',200,['transactions' => $transactions]);
    }

    public function transactionByPayment($payment_id){
        $transactions = TransactionHistory::where('payment_id',$payment_id)->get();

        return $this->successResponse('Transactions by payment retrieved.',200,['transactions' => $transactions]);
    }

    public function exportTransactionHistory(){
        $user_id = $this->getUserId();

        return Excel::download(new TransactionsExport($user_id), 'transaction_history.xlsx');
    }

    private function getUserId(){
        $user = auth()->user();
        if(!$user){
            return null;
        }
        return $user->id;
    }
}
