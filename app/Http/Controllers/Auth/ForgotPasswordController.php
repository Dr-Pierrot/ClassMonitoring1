<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send the password reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Password::sendResetLink(
            $request->only('email')
        );

        // Check the response from Password::sendResetLink
        switch ($response) {
            case Password::RESET_LINK_SENT:
                Log::info('Password reset link sent to ' . $request->email);
                return back()->with('status', 'Password reset link sent! Please check your email.');
            case Password::INVALID_USER:
                return back()->withErrors(['email' => 'Invalid email. Please check and try again.']);
            default:
                return back()->withErrors(['email' => 'Something went wrong. Please try again later.']);
        }
    }

    public function sendResetLinkEmailApi(Request $request)
    {   
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Attempt to send the password reset link
        $response = Password::sendResetLink($request->only('email'));

        // Check the response from Password::sendResetLink
        switch ($response) {
            case Password::RESET_LINK_SENT:
                // Log the event for debugging (optional)
                Log::info('Password reset link sent to ' . $request->email);

                // Return a JSON response indicating success
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent! Please check your email.'
                ], 200);

            case Password::INVALID_USER:
                // Return a JSON response indicating invalid user (wrong email)
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email. Please check and try again.'
                ], 404);

            default:
                // Return a JSON response indicating failure
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.'
                ], 500);
        }
    }
}
