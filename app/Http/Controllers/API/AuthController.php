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
            $token_result = User::generateTokenFor($user)->plainTextToken;
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

        $uuid = Str::uuid()->toString();

        try {

            $user_mysql = User::create([
                'id' => $uuid,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user_pgsql = UserPgsql::create([
                'id' => $uuid,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $tokenId = Str::uuid()->toString();
            $tokenName = 'authToken';
            $tokenPlainText = Str::random(40);
            $tokenHashed = hash('sha256', $tokenPlainText);
            $abilities = json_encode(['*']);
            $now = Carbon::now();

            DB::connection('mysql')->table('personal_access_tokens')->insert([
                'id' => $tokenId,
                'tokenable_type' => get_class($user_mysql),
                'tokenable_id' => $user_mysql->id,
                'name' => $tokenName,
                'token' => $tokenHashed,
                'abilities' => $abilities,
                'last_used_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::connection('pgsql')->table('personal_access_tokens')->insert([
                'id' => $tokenId,
                'tokenable_type' => get_class($user_pgsql),
                'tokenable_id' => $user_pgsql->id,
                'name' => $tokenName,
                'token' => $tokenHashed,
                'abilities' => $abilities,
                'last_used_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
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
            '',
            'User Created Successfully',
            $tokenPlainText
        );
    }

    // public function logout(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user && $user->currentAccessToken()) {
    //         $user->currentAccessToken()->delete();

    //         return ResponseFormatter::success(
    //             null,
    //             'Logout Successfully'
    //         );
    //     }

    //     return ResponseFormatter::success(
    //         null,
    //         'Token Not Found, or Already Logout'
    //     );
    // }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();

                return ResponseFormatter::success(
                    null,
                    'Logout Successfully'
                );
            }

            // Coba ambil token manual dari tabel `personal_access_tokens`
            $token = $request->bearerToken();
            if ($token) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

                if ($accessToken) {
                    $accessToken->delete();

                    return ResponseFormatter::success(
                        null,
                        'Logout Successfully (token manually deleted)'
                    );
                }
            }

            return ResponseFormatter::success(
                null,
                'Token Not Found or Already Logout'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Logout Error');
        }
    }
}
