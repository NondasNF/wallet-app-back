<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get the authenticated User's wallet.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([$user->wallet, 'ok' => true], 200);
    }

    /**
     * Update the authenticated User's wallet.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $status = $request->status;
        $user = $request->user();
        $wallet = $user->wallet;
        $wallet->is_active = $status;
        $wallet->save();

        return response()->json(['message' => 'Wallet status updated', 'ok' => true], 200);
    }
}
