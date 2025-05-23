<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Pgsql\UserPgsql;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'password.required' => 'Password is required'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(
                $validator->errors(),
                'Validation Error',
                422
            );
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = UserPgsql::where('email', $request->email)->first();
            }
            if (!Hash::check($request->password, $user->password, [])) {
                return ResponseFormatter::error(
                    [
                        'message' => 'Password is incorrect'
                    ],
                    'Authentication Failed',
                    401
                );
            }

            $token_result = $user->createToken('authToken')->plainTextToken;
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Error');
        }
        return ResponseFormatter::success($user->roles, 'Login Successfully', $token_result);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'email.unique' => 'Email already registered',
            'password.required' => 'Password is required'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(
                $validator->errors(),
                'Validation Error',
                422
            );
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            UserPgsql::create([
                'id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token_result = $user->createToken('authToken')->plainTextToken;
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Registration Failed',
                    'error' => $error->getMessage()
                ],
                'Error',
                500
            );
        }
        return ResponseFormatter::success(
            null,
            'User Created Successfully',
            $token_result
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            return ResponseFormatter::success(
                null,
                'Logout Successfully'
            );
        }

        return ResponseFormatter::success(
            null,
            'Token Not Found, or Already Logout'
        );
    }
}
