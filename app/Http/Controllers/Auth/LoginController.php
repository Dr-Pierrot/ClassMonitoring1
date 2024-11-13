<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Generate a token for the authenticated user
            $user = Auth::user();
            $token = $user->createToken('loginToken')->plainTextToken;

            // Send the token and redirect to the dashboard
            if($user->user_type == 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not allowed.',
                ]);
            }
            return response()->json([
                'success' => true,
                'token' => $token, // Return the token
            ]);
        }

        // If login fails, send back an error response
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
