<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => strtolower(trim($validated['email']))],
            ['subscribed_at' => now()]
        );

        return response()->json([
            'success' => true,
            'message' => 'You are on the list! Watch your inbox for new drops and releases.',
        ]);
    }
}
