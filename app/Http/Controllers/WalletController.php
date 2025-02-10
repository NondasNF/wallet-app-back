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
        return response()->json($user->wallet, 200);
    }
}
