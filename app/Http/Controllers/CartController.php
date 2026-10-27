<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request);
        $totals = $this->cartService->getTotals($cart);

        if ($request->query('fragment')) {
            return view('partials.cart-fragment', compact('cart', 'totals'));
        }

        return view('pages.cart', compact('cart', 'totals'));
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_label' => 'nullable|string|max:10',
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        $cart = $this->cartService->getCart($request);
        $size = $request->input('size_label', '16') ?: '16';
        $quantity = (int) $request->input('quantity', 1);

        $this->cartService->addItem($cart, $request->input('product_id'), $size, $quantity);

        $freshCart = $this->cartService->getCart($request);

        return response()->json([
            'success' => true,
            'message' => 'Piece added to your bag!',
            'cart_count' => $freshCart->total_quantity,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:0|max:10',
            'size_label' => 'nullable|string|max:10',
        ]);

        $cart = $this->cartService->getCart($request);
        $this->cartService->updateItem(
            $cart,
            $request->input('item_id'),
            $request->has('quantity') ? (int) $request->input('quantity') : null,
            $request->input('size_label')
        );

        $freshCart = $this->cartService->getCart($request);

        return response()->json([
            'success' => true,
            'cart_count' => $freshCart->total_quantity,
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $cart = $this->cartService->getCart($request);
        $this->cartService->removeItem($cart, $request->input('item_id'));

        $freshCart = $this->cartService->getCart($request);

        return response()->json([
            'success' => true,
            'cart_count' => $freshCart->total_quantity,
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string|max:30',
        ]);

        $cart = $this->cartService->getCart($request);
        $res = $this->cartService->applyCoupon($cart, $request->input('coupon_code'));

        return response()->json($res);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request);
        $this->cartService->removeCoupon($cart);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
        ]);
    }
}
