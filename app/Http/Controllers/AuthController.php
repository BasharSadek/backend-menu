<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function me(Request $request)
    {
        $user =  $request->user();
        return response()->json([
            'user' => $user,
        ], 200);
    }
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'          => $request->all()['name'],
            'email'         => $request->all()['email'],
            'password'      => Hash::make($request->all()['password']),
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Successful created user.',
            'user' => $user,
        ], 201);
    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }
        // Check email
        $user = User::where('email', $request->all()['email'])->first();

        // Check password
        if (!$user || !Hash::check($request->all()['password'], $user->password)) {
            return response([
                'message' => 'The password is incorrect'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token
        ];

        return response($response, 200);
    }

    public function logout(Request $request)
    {

        $user = $request->user();
        $user->tokens()->delete();

        return response([
            'success' => true,
            'message' => 'Logged out'
        ], 200);
    }
  
}
