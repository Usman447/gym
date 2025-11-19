<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $settings = \Utilities::getSettings();

        return view('settings.show', compact('settings'));
    }

    public function edit()
    {
        $settings = \Utilities::getSettings();

        return view('settings.edit', compact('settings'));
    }

    public function save(Request $request)
    {
        // Get All Inputs Except '_Token' to loop through and save
        $settings = $request->except('_token');

        // Update All Settings
        foreach ($settings as $key => $value) {
            if ($key == 'gym_logo') {
                \Utilities::uploadFile($request, '', $key, 'gym_logo', \constPaths::GymLogo); // Upload File
                $value = $key.'.jpg'; // Image Name For DB
            }

            // Skip updating whatsapp_api_secret if it's empty (to keep current value)
            // But only if the setting already exists in database
            if ($key == 'whatsapp_api_secret' && empty($value)) {
                $existing = Setting::where('key', '=', $key)->first();
                if ($existing && !empty($existing->value)) {
                    continue; // Skip if setting exists and has a value, and new value is empty
                }
                // If setting doesn't exist or is empty, we'll save the empty value (user can update later)
            }

            // Update or create the setting
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'updated_at' => \Carbon\Carbon::now()]
            );
        }

        flash()->success('Setting was successfully updated');

        return redirect('settings/edit');
    }
}
