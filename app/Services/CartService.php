<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get or create the cart for current request (User or Session token).
     */
    public function getCart(Request $request): Cart
    {
        $user = $request->user();

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // If there is also a guest session cart, merge it into user's cart
            $sessionToken = $request->session()->get('cart_session_token');
            if ($sessionToken) {
                $this->mergeGuestCart($sessionToken, $user);
                $request->session()->forget('cart_session_token');
            }

            return $cart->load(['items.product.images', 'items.product.category', 'items.product.metal']);
        }

        $sessionToken = $request->session()->get('cart_session_token');
        if (! $sessionToken) {
            $sessionToken = Str::random(40);
            $request->session()->put('cart_session_token', $sessionToken);
        }

        $cart = Cart::firstOrCreate(['session_token' => $sessionToken]);
        return $cart->load(['items.product.images', 'items.product.category', 'items.product.metal']);
    }

    /**
     * Merge items from guest session into user's cart on login.
     */
    public function mergeGuestCart(string $sessionToken, User $user): void
    {
        $guestCart = Cart::where('session_token', $sessionToken)->first();
        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $existing = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->where('size_label', $guestItem->size_label)
                ->first();

            if ($existing) {
                $existing->quantity = min(10, $existing->quantity + $guestItem->quantity);
                $existing->save();
            } else {
                $guestItem->cart_id = $userCart->id;
                $guestItem->save();
            }
        }

        if ($guestCart->coupon_code && ! $userCart->coupon_code) {
            $userCart->coupon_code = $guestCart->coupon_code;
            $userCart->save();
        }

        $guestCart->delete();
    }

    /**
     * Add an item to cart.
     */
    public function addItem(Cart $cart, int $productId, string $sizeLabel = '16', int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);
        $quantity = max(1, min(10, $quantity));

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('size_label', $sizeLabel)
            ->first();

        if ($item) {
            $item->quantity = min(10, $item->quantity + $quantity);
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'size_label' => $sizeLabel,
                'quantity' => $quantity,
            ]);
        }

        return $item;
    }

    /**
     * Update item size or quantity.
     */
    public function updateItem(Cart $cart, int $itemId, ?int $quantity = null, ?string $sizeLabel = null): ?CartItem
    {
        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->firstOrFail();

        if ($quantity !== null) {
            if ($quantity <= 0) {
                $item->delete();
                return null;
            }
            $item->quantity = min(10, $quantity);
        }

        if ($sizeLabel !== null && $sizeLabel !== $item->size_label) {
            // Check if item with this new size already exists in cart
            $existing = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $item->product_id)
                ->where('size_label', $sizeLabel)
                ->where('id', '!=', $item->id)
                ->first();

            if ($existing) {
                $existing->quantity = min(10, $existing->quantity + $item->quantity);
                $existing->save();
                $item->delete();
                return $existing;
            }

            $item->size_label = $sizeLabel;
        }

        $item->save();
        return $item;
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(Cart $cart, int $itemId): bool
    {
        return (bool) CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Cart $cart, string $code): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            return ['success' => false, 'message' => 'Coupon code is invalid.'];
        }

        $subtotal = $cart->subtotal;
        if (! $coupon->isValidFor($subtotal)) {
            $msg = $coupon->min_order > 0 && $subtotal < $coupon->min_order
                ? "This coupon requires a minimum order of " . Product::formatInr($coupon->min_order) . "."
                : "This coupon is expired or inactive.";
            return ['success' => false, 'message' => $msg];
        }

        $cart->coupon_code = $coupon->code;
        $cart->save();

        return ['success' => true, 'message' => "Coupon '{$coupon->code}' applied successfully!"];
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(Cart $cart): void
    {
        $cart->coupon_code = null;
        $cart->save();
    }

    /**
     * Compute full price totals, discounts and breakdown.
     */
    public function getTotals(Cart $cart): array
    {
        $items = $cart->items()->with('product.images')->get();

        $subtotal = 0.0;
        $mrpTotal = 0.0;
        $totalQty = 0;

        foreach ($items as $item) {
            $price = (float) ($item->product->price ?? 0);
            $mrp = (float) ($item->product->mrp ?? $price);
            $mrpTotal += max($mrp, $price) * $item->quantity;
            $subtotal += $price * $item->quantity;
            $totalQty += $item->quantity;
        }

        $catalogDiscount = max(0, $mrpTotal - $subtotal);

        // Coupon discount
        $couponDiscount = 0.0;
        $coupon = null;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            } else {
                $cart->coupon_code = null;
                $cart->save();
            }
        }

        $afterCoupon = max(0, $subtotal - $couponDiscount);

        // Delivery fee from settings
        $threshold = (float) Setting::get('free_delivery_threshold', 0);
        $deliveryFee = (float) Setting::get('delivery_fee', 0);

        if ($threshold > 0 && $afterCoupon >= $threshold) {
            $deliveryFee = 0.0;
        }

        $total = $afterCoupon + $deliveryFee;
        $totalSavings = $catalogDiscount + $couponDiscount;

        return [
            'items' => $items,
            'count' => $totalQty,
            'mrp_total' => $mrpTotal,
            'subtotal' => $subtotal,
            'catalog_discount' => $catalogDiscount,
            'coupon' => $coupon,
            'coupon_discount' => $couponDiscount,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'total_savings' => $totalSavings,
            'formatted_mrp' => Product::formatInr($mrpTotal),
            'formatted_subtotal' => Product::formatInr($subtotal),
            'formatted_coupon_discount' => Product::formatInr($couponDiscount),
            'formatted_delivery' => $deliveryFee > 0 ? Product::formatInr($deliveryFee) : 'Free',
            'formatted_total' => Product::formatInr($total),
            'formatted_savings' => Product::formatInr($totalSavings),
        ];
    }
}
