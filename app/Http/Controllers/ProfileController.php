<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'gcash_name' => 'nullable|string|max:255',
            'gcash_number' => 'nullable|regex:/^[0-9]{8,20}$/',
            'gcash_qr' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['name'] = trim(html_entity_decode($validated['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        $user = auth()->user();

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        if (! $user->isFarmer()) {
            unset($validated['gcash_name'], $validated['gcash_number'], $validated['gcash_qr']);
        } elseif ($request->hasFile('gcash_qr')) {
            if ($user->gcash_qr) {
                Storage::disk(config('filesystems.default'))->delete($user->gcash_qr);
            }

            $validated['gcash_qr'] = $request->file('gcash_qr')->storePublicly('gcash_qr_codes', config('filesystems.default'));
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the change password form.
     */
    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password changed successfully!');
    }
}
