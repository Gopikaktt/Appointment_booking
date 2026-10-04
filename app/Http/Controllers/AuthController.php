<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Check if user account is deactivated
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated by an administrator or is pending approval.',
                ]);
            }

            // Check if doctor account is pending approval
            if ($user->isDoctor()) {
                $doctor = Doctor::where('user_id', $user->id)->first();
                if ($doctor && $doctor->status === 'inactive') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your doctor account is pending administrator approval. Please wait for an administrator to activate your profile.',
                    ]);
                }
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required|in:patient,doctor',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|unique:doctors,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required_if:role,doctor|nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $role = $request->role;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        if ($role === 'doctor') {
            // Create Doctor profile as inactive (pending administrator approval)
            Doctor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'specialization' => $request->specialization ?? 'General Physician',
                'email' => $request->email,
                'phone' => $request->phone ?? 'N/A',
                'bio' => 'Registered specialist doctor.',
                'consultation_fee' => 100.00,
                'room_number' => 'Consultation Room',
                'available_days' => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                'start_time' => '09:00',
                'end_time' => '17:00',
                'slot_duration_minutes' => 30,
                'status' => 'inactive',
            ]);

            return redirect()->route('login')->with('success', "Registration submitted! Your doctor account has been created and is currently pending administrator approval. You will be able to sign in once an administrator approves your profile.");
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', "Welcome {$user->name}! Your account has been registered successfully.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        return redirect()->route('password.reset', ['email' => $request->email])
            ->with('success', 'Email verified. Please set your new password below.');
    }

    public function showResetPassword(Request $request, $email = null)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $email = $email ?? $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid or expired password reset request.']);
        }

        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please sign in with your new password.');
        }

        return back()->withErrors(['email' => 'Failed to reset password. User account not found.']);
    }
}
