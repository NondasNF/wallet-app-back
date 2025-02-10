<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;
        $wallet->balance = bcadd($wallet->balance, $request->amount, 2);
        $wallet->save();
        Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'deposit',
        ]);

        return response()->json(['message' => 'Deposit successful', 'balance' => $wallet->balance]);
    }
}
