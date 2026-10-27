<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with(['product', 'user'])->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApprove(Review $review)
    {
        $review->is_approved = ! $review->is_approved;
        $review->save();

        // Refresh product avg rating
        $product = $review->product;
        if ($product) {
            $approved = $product->reviews()->where('is_approved', true);
            $product->rating_avg = round((float) $approved->avg('rating'), 2);
            $product->rating_count = $approved->count();
            $product->save();
        }

        $state = $review->is_approved ? 'approved' : 'hidden';
        return back()->with('success', "Review #{$review->id} has been {$state}.");
    }

    public function destroy(Review $review)
    {
        $product = $review->product;
        $review->delete();

        if ($product) {
            $approved = $product->reviews()->where('is_approved', true);
            $product->rating_avg = round((float) $approved->avg('rating'), 2);
            $product->rating_count = $approved->count();
            $product->save();
        }

        return back()->with('success', 'Review deleted.');
    }
}
