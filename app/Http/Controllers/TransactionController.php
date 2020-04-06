<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index() {
        $transactions = Transaction::all();
        return response()->json($transactions, 200);
    }

    public function show(Request $request)
    {
        $transaction = Transaction::find($request->id);

        if($transaction != null)
            return response()->json(['transaction' => $transaction], 200);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function markAsProcessed(Request $request) {
        $transaction = Transaction::find($request->id);

        if($transaction) {
            $transaction->processed = 1;
            $transaction->save();
            return response()->json(['success' => true], 200);
        } else
            return response()->json(['message' => 'There was an error'], 500);
    }

    public function cardPayment(Request $request){
        $transaction = new Transaction();

        $transaction->cart_data = $request->cart;
        $transaction->delivery_data = $request->livrare;
        $transaction->fiscal_data = $request->facturare;

        if($transaction->save())
            return response()->json(['txn' => $transaction->id ], 200);
        else
            return response()->json(['message' => 'There was an error'], 500);
    }
}
