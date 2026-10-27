<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessageAlert;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:5|max:2000',
        ]);

        $contact = ContactMessage::create($validated);

        // Send alert to store email
        $alertEmail = env('ADMIN_ALERT_EMAIL', Setting::get('contact_email', 'daisyjewelers940@gmail.com'));
        try {
            if ($alertEmail) {
                Mail::to($alertEmail)->send(new NewContactMessageAlert($contact));
                Log::info("Contact message alert dispatched to {$alertEmail}");
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch contact alert email: " . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Thank you! Your message has been received. Our team will contact you shortly.']);
        }

        return back()->with('success', 'Thank you! Your message has been sent. We will get in touch shortly.');
    }
}
