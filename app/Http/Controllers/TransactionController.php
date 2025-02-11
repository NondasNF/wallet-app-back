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

        if ($wallet->is_active == 0) {
            return response()->json(['message' => 'Your wallet is inactive', 'ok' => false], 400);
        }

        $wallet->balance = bcadd($wallet->balance, $request->amount, 2);
        $wallet->save();

        Transaction::create([
            'from_user_id' => $user->id,
            'to_user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'deposit',
        ]);

        return response()->json(['message' => 'Deposit successful', 'balance' => $wallet->balance, 'ok' => true]);
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
        if (!$toWallet) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $toUser = $toWallet->user;
        $user = $request->user();
        $wallet = $user->wallet;

        if ($wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance', 'ok' => false], 400);
        }

        if ($user->id == $toUser->id) {
            return response()->json(['message' => 'Cannot transfer to the same account', 'ok' => false], 400);
        }

        if ($toWallet->is_active == 0) {
            return response()->json(['message' => 'User wallet destination is inactive', 'ok' => false], 400);
        }

        if ($wallet->is_active == 0) {
            return response()->json(['message' => 'Your wallet is inactive', 'ok' => false], 400);
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

        return response()->json(['message' => 'Transfer successful', 'balance' => $wallet->balance, 'ok' => true]);
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

    /**
     * Cancel a transaction
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $transaction = Transaction::where('id', $id)
            ->where('from_user_id', $user->id)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found', 'ok' => false], 404);
        }

        if ($transaction->type == 'deposit') {
            return response()->json(['message' => 'Cannot cancel a deposit transaction', 'ok' => false], 400);
        }

        $wallet = $user->wallet;
        $wallet->balance = bcadd($wallet->balance, $transaction->amount, 2);
        $wallet->save();
        $toWallet = Wallet::find($transaction->to_user_id);
        $toWallet->balance = bcsub($toWallet->balance, $transaction->amount, 2);
        $toWallet->save();
        $transaction->type = 'cancelled';
        $transaction->save();

        return response()->json(['message' => 'Transaction cancelled', 'ok' => true]);
    }
}
