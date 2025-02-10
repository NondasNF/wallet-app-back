<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;

class TransactionController extends Controller
{
    /**
     * Deposit money to the user's wallet
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Transfer money to another user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:users,id',
        ]);

        $toWallet = Wallet::find($request->wallet_id);
        if(!$toWallet) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $toUser = $toWallet->user;
        $user = $request->user();
        $wallet = $user->wallet;


        if ($wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        if ($user->id == $toUser->id) {
            return response()->json(['message' => 'Cannot transfer to the same account'], 400);
        }

        $wallet->balance = bcsub($wallet->balance, $request->amount, 2);
        $wallet->save();
        $toWallet->balance = bcadd($toWallet->balance, $request->amount, 2);
        $toWallet->save();

        Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $toUser->id,
            'amount' => $request->amount,
            'type' => 'transfer',
        ]);

        return response()->json(['message' => 'Transfer successful', 'balance' => $wallet->balance]);
    }

    /**
     * Get the transaction history of the user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $perPage = $request->per_page ?? 10;

        $transactions = Transaction::where('from_user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        return response()->json($transactions);
    }
}
