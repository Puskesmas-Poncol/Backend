<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                "email" => "required",
                'password' => "required"
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return ResponseHelper::baseResponse("Email not found", 404);
            }

            $key = 'example_key';

            if (!Hash::check($validated['password'], $user->password)) {
                return ResponseHelper::baseResponse("Your password is wrong", 404);
            }

            $payload = [
                'iat' => intval(microtime(true)),
                'exp' => intval(microtime(true)) + 60 * 60 * 2,
                'uid' => $user->id
            ];

            $token = JWT::encode($payload, env("JWT_SECRET"), "HS256");
            return ResponseHelper::baseResponse("Login success", 200, [
                "token" => $token,
                "userType" => $user->user_type,
            ]);
        } catch (Exception $err) {
            return ResponseHelper::err($err->getMessage());
        }
    }

    public function signup(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                "email" => 'required|email|max:255|unique:users,email',
                "name" => 'required|max:255',
                'password' => 'required',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return ResponseHelper::baseResponse("Account Success Created", 200, $user, null);
        } catch (Exception $err) {
            return ResponseHelper::err($err->getMessage());
        }
    }
}
