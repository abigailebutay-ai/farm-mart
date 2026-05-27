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
            'notification_enabled' => 'nullable|boolean',
        ]);

        auth()->user()->update([
            'notification_enabled' => $request->has('notification_enabled') ? true : false,
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }
}
