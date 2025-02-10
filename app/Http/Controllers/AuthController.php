<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
  public function auth(Request $request)
  {
    $user = Auth::user();
    if ($user) {
      $token = $user->createToken('JWT')->plainTextToken;
      return response()->json(['token' => $token, 'user' => $user], 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
  }

  public function user(Request $request) {
    return $request->user();
  }

  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');
  
    if (Auth::attempt($credentials)) {
      $user = Auth::user();
      $token = $user->createToken('JWT')->plainTextToken;
      return response()->json(['token' => $token, 'user' => $user], 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
  }

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
    $token = $user->createToken('JWT')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user], 201);
  }
}
