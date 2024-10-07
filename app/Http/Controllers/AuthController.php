<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'No account found with this email address.'
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'The password you entered is incorrect.'
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('dashboard')->with('alert', [
            'type' => 'success',
            'message' => 'Login successful! Welcome back.'
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('alert', [
            'type' => 'success',
            'message' => 'You have been successfully logged out.'
        ]);
    }


}
