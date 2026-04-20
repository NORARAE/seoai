<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends Controller
{
    public function show()
    {
        return view('dashboard.settings.notifications', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        Auth::user()->update([
            'email_marketing_opt_in' => $request->boolean('email_marketing_opt_in'),
            'email_product_updates' => $request->boolean('email_product_updates'),
            'email_scan_notifications' => $request->boolean('email_scan_notifications'),
        ]);

        return back()->with('status', 'Notification preferences saved.');
    }
}
