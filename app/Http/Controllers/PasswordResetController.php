<?php

namespace App\Http\Controllers;

use App\Customer;
use App\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Hash;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            $failure_msg = [
                "status" => 0,
                "message" => "No customer found against this email.",
            ];
            return response()->json($failure_msg, 404);
        }

        PasswordReset::where('email', $request->email)->delete();

        $token = Str::random(60);
        $passwordReset = new PasswordReset();
        $passwordReset->email = $request->email;
        $passwordReset->token = $token;
        $passwordReset->created_at = now();
        $passwordReset->save();

        $customer->token = $token;
        $data = [
            'customer' => $customer,
            'token' => $token,
        ];

        try {
            Mail::send('emails.passwordreset', $data, function ($message) use ($customer) {
                $message->from('noreply@makwaapp.com', 'Makwa');
                $message->to($customer->email)->subject('Password Reset Email');
            });
        } catch (\Throwable $e) {
            // Log mail sending error safely without interrupting response flow
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        $success_msg = [
            "status" => 1,
            "message" => "Email has been sent.",
        ];

        return response()->json($success_msg, 200);
    }

    public function resetPassword($token)
    {
        $reset = PasswordReset::where('token', $token)->first();

        if (!$reset || (now()->subHours(2)->greaterThan($reset->created_at))) {
            return abort(404);
        }

        return view('resetpassword', ['email' => $reset->email, 'token' => $token]);
    }

    public function changePassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        $reset = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid or expired password reset token.'
                ], 422);
            }
            return redirect()->back()->withErrors(['token' => 'Invalid or expired password reset token.']);
        }

        if (now()->subHours(2)->greaterThan($reset->created_at)) {
            $reset->delete();
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Password reset token has expired.'
                ], 422);
            }
            return redirect()->back()->withErrors(['token' => 'Password reset token has expired.']);
        }

        $customer = Customer::where('email', $request->email)->first();
        if ($customer) {
            $customer->password = Hash::make($request->password);
            $customer->save();
        }

        PasswordReset::where('email', $request->email)->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 1,
                'message' => 'Password has been reset successfully.'
            ], 200);
        }

        return redirect()->route('success');
    }

    public function showSuccess()
    {
        return view('resetsuccessfull');
    }
}
