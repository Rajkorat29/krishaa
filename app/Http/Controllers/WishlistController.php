<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user) {
            $productIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
        } else {
            $productIds = $request->session()->get('guest_wishlist', []);
        }

        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->with(['category', 'metal', 'images'])
            ->get();

        return view('pages.wishlist', [
            'products' => $products,
            'wishedProductIds' => $productIds,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = (int) $request->input('product_id');
        $user = $request->user();

        if ($user) {
            $existing = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();
            if ($existing) {
                $existing->delete();
                $added = false;
                $msg = 'Removed from wishlist';
            } else {
                Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
                $added = true;
                $msg = 'Saved to wishlist!';
            }

            $count = Wishlist::where('user_id', $user->id)->count();
        } else {
            $list = $request->session()->get('guest_wishlist', []);
            $idx = array_search($productId, $list);
            if ($idx !== false) {
                unset($list[$idx]);
                $list = array_values($list);
                $added = false;
                $msg = 'Removed from wishlist';
            } else {
                $list[] = $productId;
                $added = true;
                $msg = 'Saved to wishlist!';
            }

            $request->session()->put('guest_wishlist', $list);
            $count = count($list);
        }

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => $count,
            'message' => $msg,
        ]);
    }
}
