<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Show the form to change the password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if the current password is correct
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        try {
            // Update the user's password
            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Log the user out after the password change
            Auth::logout();

            // Redirect to login with a success message
            return redirect()->route('login')->with('status', 'Password successfully changed. Please log in again.');

        } catch (\Exception $e) {
            // Handle unexpected errors
            return back()->withErrors(['error' => 'An error occurred while updating the password. Please try again.']);
        }
    }
    public function updatePasswordApi(Request $request)
    {
        // Check if the current password is correct
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password is incorrect.'
        ]);
        }

        // Update the user's password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log the user out by revoking their tokens
        $user->tokens()->delete();

        // Return a success message
        return response()->json([
            'success' => true,
            'message' => 'Password successfully changed. Please log in again.',
        ]);
    }
}
