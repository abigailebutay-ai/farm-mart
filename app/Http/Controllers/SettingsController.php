<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        return view('settings.index', ['user' => auth()->user()]);
    }

    /**
     * Update user settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'dark_mode' => 'nullable|boolean',
            'notification_enabled' => 'nullable|boolean',
        ]);

        auth()->user()->update([
            'dark_mode' => $request->has('dark_mode') ? true : false,
            'notification_enabled' => $request->has('notification_enabled') ? true : false,
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }
}
