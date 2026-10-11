<?php

namespace App\Http\Controllers\Api\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Helper\EmailHelperController;
use Illuminate\Http\Request;
use Hash;

class VendorAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:vendor-api', ['except' => ['login', 'register']]);
    }

    public function testfn()
    {
        return "jwt api test.";
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth('vendor-api')->attempt($credentials)) {
            // return response()->json(['error' => 'Unauthorized'], 401);
            $failure_msg = array(
                "status" => 0,
                "message" => "Incorrect email or password. Please enter valid credentials.",
            );
            return json_encode($failure_msg, JSON_NUMERIC_CHECK);
        }
        $vendor =  $this->guard()->user();
        
        return response()->json([
            "status" => 1,
            "message" => "Vendor Logged In Successfully.",
            'access_token' => $token,
            'vendor' => $vendor,
            'token_type' => 'bearer',
            'expires_in' => auth('vendor-api')->factory()->getTTL() * 60
        ]);
        // return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        // need to use laravel validate !!!
        //    Vendor register
        // return redirect()->intended('login/Vendor');
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('vendor-api')->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('vendor-api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('vendor-api')->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'user' => $this->guard()->user(),
            'token_type' => 'bearer',
            'expires_in' => auth('vendor-api')->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return \Auth::Guard('vendor-api');
    }


    // ======================================== Helper ==============================



    public function handelReturnResultFail($msg)
    {
        $failure_array = array(
            "status" => 0,
            "message" => $msg
        );
        return json_encode($failure_array, JSON_NUMERIC_CHECK);
    }
}
