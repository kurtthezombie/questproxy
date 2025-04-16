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
        // Get logged-in user id
        $user_id = $this->getUserId();
    
        // Prepare the query
        $query = TransactionHistory::with('payment') // Eager load the payment relation
            ->whereHas('payment', function ($query) use ($user_id) {
                $query->where('payer_id', $user_id);
            });
    
        // Apply the search if a search query exists
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $column = $request->get('column', 'id'); // Get column name from request, default to 'id'
    
            // Apply different search column based on the provided column
            $query->where(function ($query) use ($search, $column) {
                // Apply the search filter on the transaction_history table first
                $query->where($column, 'LIKE', "%$search%");
    
                // If searching by payment's amount, apply filter to the related payment table
                if ($column === 'amount') {
                    $query->orWhereHas('payment', function ($query) use ($search) {
                        $query->where('payments.amount', 'LIKE', "%$search%");
                    });
                }
            });
        }
    
        // Paginate the results, 10 per page
        $transactions = $query->orderBy('updated_at', 'desc')->paginate(10);
    
        // Check if no transactions found
        if ($transactions->isEmpty()) {
            return $this->successResponse('No transactions found for this user.', 200, ['user_id' => $user_id]);
        }
    
        // Return the transactions with pagination info
        return $this->successResponse('Transactions retrieved.', 200, [
            'transactions' => $transactions->items(),
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'next_page_url' => $transactions->nextPageUrl(),
        ]);
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
