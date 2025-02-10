<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Wallet;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function auth(Request $request)
  {
    $info = $request->device_name;
    $user = Auth::user();
    if ($user) {
      $token = $user->createToken($info['device_name'])->plainTextToken;
      return response()->json(['token' => $token, 'user' => $user], 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function user(Request $request) {
    return $request->user();
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
    $deviceName = $request->userAgent();
    $credentials = $request->only('email', 'password');
  
    if (Auth::attempt($credentials)) {
      $user = Auth::user();
      $token = $user->createToken($deviceName)->plainTextToken;
      return response()->json(['token' => $token, 'user' => $user], 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
  }

  /**
   * Register a User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    $formData = $request->only('name', 'email', 'password', 'password_confirmation');

    $validator = Validator::make($formData, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'password_confirmation' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::create($formData);
    Wallet::create(['user_id' => $user->id, 'balance' => 0]);
    $token = $user->createToken('JWT')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user], 201);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out'], 200);
  }

  public function logoutAll(Request $request)
  {
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out from all devices'], 200);
  }

  public function loggedDevices(Request $request)
  {
    return response()->json($request->user()->tokens, 200);
  }
}
