<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Metal;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function rings(Request $request)
    {
        return $this->index($request, 'rings');
    }

    public function earrings(Request $request)
    {
        return $this->index($request, 'earrings');
    }

    public function bangles(Request $request)
    {
        return $this->index($request, 'bangles');
    }

    public function necklaces(Request $request)
    {
        return $this->index($request, 'necklaces');
    }

    public function pearls(Request $request)
    {
        return $this->index($request, 'pearls');
    }

    public function index(Request $request, ?string $categorySlug = null)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $metals = Metal::orderBy('name')->get();

        $category = null;
        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
        }

        $query = Product::where('is_active', true)->with(['category', 'metal', 'images']);

        if ($category) {
            $query->where('category_id', $category->id);
        }

        // Search query
        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('stone', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhereHas('metal', function ($m) use ($q) {
                        $m->where('name', 'like', "%{$q}%");
                    });
            });
        }

        // Max price filter
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // Metal filter
        if ($metalsFilter = $request->input('metals')) {
            $metalNames = is_array($metalsFilter) ? $metalsFilter : explode(',', $metalsFilter);
            $query->whereHas('metal', function ($m) use ($metalNames) {
                $m->whereIn('name', $metalNames);
            });
        }

        // Rating filter
        if ($minRating = (float) $request->input('rating')) {
            $query->where('rating_avg', '>=', $minRating);
        }

        // In stock only
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Wishlist only
        if ($request->boolean('wish_only')) {
            $user = $request->user();
            $wishedIds = $user
                ? \App\Models\Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray()
                : session()->get('guest_wishlist', []);
            $query->whereIn('id', $wishedIds);
        }

        // Sorting
        $sort = $request->input('sort', 'pop');
        switch ($sort) {
            case 'low':
                $query->orderBy('price', 'asc');
                break;
            case 'high':
                $query->orderBy('price', 'desc');
                break;
            case 'rate':
                $query->orderBy('rating_avg', 'desc')->orderBy('rating_count', 'desc');
                break;
            case 'new':
                $query->orderBy('id', 'desc');
                break;
            case 'pop':
            default:
                $query->orderBy('rating_count', 'desc')->orderBy('id', 'desc');
                break;
        }

        $totalCount = (clone $query)->count();
        $products = $query->paginate(24);

        if ($request->ajax() || $request->wantsJson()) {
            $user = $request->user();
            $wishedProductIds = $user
                ? \App\Models\Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray()
                : session()->get('guest_wishlist', []);

            $html = '';
            foreach ($products as $prod) {
                $html .= view('partials.product-card', [
                    'product' => $prod,
                    'wishedProductIds' => $wishedProductIds,
                ])->render();
            }

            return response()->json([
                'html' => $html,
                'count' => $products->count(),
                'total' => $totalCount,
            ]);
        }

        return view('pages.shop', compact('products', 'category', 'categories', 'metals', 'totalCount'));
    }

    public function show(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int)$slug : 0)
            ->with(['category', 'metal', 'images', 'sizes', 'approvedReviews.user'])
            ->firstOrFail();

        $user = $request->user();
        $wishedProductIds = $user
            ? \App\Models\Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray()
            : session()->get('guest_wishlist', []);

        if ($request->query('modal')) {
            return view('partials.product-detail-modal-fragment', compact('product', 'wishedProductIds'));
        }

        return view('pages.product-detail', compact('product', 'wishedProductIds'));
    }
}
