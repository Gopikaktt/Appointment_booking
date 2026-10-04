<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display patient / user profile
     */
    public function show()
    {
        $user = Auth::user();

        // Patient appointment stats summary
        $stats = [
            'total' => Appointment::where('patient_id', $user->id)->count(),
            'pending' => Appointment::where('patient_id', $user->id)->where('status', 'Pending')->count(),
            'confirmed' => Appointment::where('patient_id', $user->id)->where('status', 'Confirmed')->count(),
            'completed' => Appointment::where('patient_id', $user->id)->where('status', 'Completed')->count(),
            'cancelled' => Appointment::where('patient_id', $user->id)->where('status', 'Cancelled')->count(),
        ];

        return view('profile.show', compact('user', 'stats'));
    }

    /**
     * Update user profile & security settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        // Handle password update if provided
        if (!empty($request->new_password)) {
            if (empty($request->current_password)) {
                return back()->withErrors(['current_password' => 'Please enter your current password to set a new password.'])->withInput();
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account password.'])->withInput();
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Your profile details and security settings have been updated successfully!');
    }
}
