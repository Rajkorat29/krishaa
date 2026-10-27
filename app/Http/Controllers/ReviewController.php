<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:120',
            'body' => 'required|string|min:6|max:1000',
        ]);

        $user = $request->user();

        Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_approved' => true,
        ]);

        // Recalculate product rating_avg and rating_count
        $product = Product::findOrFail($validated['product_id']);
        $approved = $product->reviews()->where('is_approved', true);
        $product->rating_avg = round((float) $approved->avg('rating'), 2);
        $product->rating_count = $approved->count();
        $product->save();

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }
}
