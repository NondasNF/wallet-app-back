<?php

namespace App\Http\Controllers;

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

        return response()->json(['message' => 'Deposit successful', 'balance' => $wallet->balance]);
    }
}
