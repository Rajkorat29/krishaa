<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Process checkout and place order inside DB transaction with stock row locks.
     */
    public function placeOrder(User $user, Cart $cart, array $addressData, string $paymentMethod = 'COD', ?string $notes = null): Order
    {
        $totals = $this->cartService->getTotals($cart);

        if (empty($totals['items']) || $totals['count'] === 0) {
            throw new Exception('Your shopping bag is empty.');
        }

        return DB::transaction(function () use ($user, $cart, $totals, $addressData, $paymentMethod, $notes) {
            // 1. Validate and lock stock for each item
            foreach ($totals['items'] as $item) {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if (! $product || ! $product->is_active) {
                    throw new Exception("Product '{$item->product->name}' is currently unavailable.");
                }

                if ($product->stock < $item->quantity) {
                    throw new Exception("Insufficient stock for '{$product->name}'. Available: {$product->stock}.");
                }

                // If size is selected, lock and check size stock
                if ($item->size_label && $item->size_label !== 'Standard') {
                    $sizeRecord = ProductSize::where('product_id', $product->id)
                        ->where('size_label', $item->size_label)
                        ->lockForUpdate()
                        ->first();

                    if ($sizeRecord && $sizeRecord->stock < $item->quantity) {
                        throw new Exception("Size {$item->size_label} for '{$product->name}' has only {$sizeRecord->stock} in stock.");
                    }
                }
            }

            // 2. Decrement stock
            foreach ($totals['items'] as $item) {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                $product->decrement('stock', $item->quantity);

                if ($item->size_label && $item->size_label !== 'Standard') {
                    $sizeRecord = ProductSize::where('product_id', $product->id)
                        ->where('size_label', $item->size_label)
                        ->lockForUpdate()
                        ->first();

                    if ($sizeRecord) {
                        $sizeRecord->decrement('stock', $item->quantity);
                    }
                }
            }

            // 3. Handle Coupon usage increment
            if ($totals['coupon']) {
                $coupon = Coupon::where('id', $totals['coupon']->id)->lockForUpdate()->first();
                if ($coupon) {
                    $coupon->increment('used_count');
                }
            }

            // 4. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $addressData['name'],
                'customer_email' => $user->email,
                'customer_phone' => $addressData['phone'],
                'shipping_address_line' => $addressData['address_line'],
                'shipping_city' => $addressData['city'],
                'shipping_state' => $addressData['state'],
                'shipping_pincode' => $addressData['pincode'],
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['coupon_discount'],
                'delivery_fee' => $totals['delivery_fee'],
                'total' => $totals['total'],
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'COD' ? 'pending' : 'paid',
                'order_status' => 'placed',
                'coupon_code' => $totals['coupon']?->code,
                'notes' => $notes,
            ]);

            // 5. Create Order Items
            foreach ($totals['items'] as $item) {
                $cover = $item->product->coverImage();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'size_label' => $item->size_label,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                    'mrp' => $item->product->mrp ?? $item->product->price,
                    'image_snapshot' => $cover,
                ]);
            }

            // 6. Clear user cart
            $cart->items()->delete();
            $cart->coupon_code = null;
            $cart->save();

            // 7. Send Emails (Admin Alert + Customer Confirmation)
            $alertEmail = env('ADMIN_ALERT_EMAIL', \App\Models\Setting::get('contact_email', 'daisyjewelers940@gmail.com'));
            try {
                if ($alertEmail) {
                    Mail::to($alertEmail)->send(new \App\Mail\NewOrderAdminAlert($order));
                }
                if (!empty($user->email)) {
                    Mail::to($user->email)->send(new \App\Mail\OrderCustomerConfirmation($order));
                }
                Log::info("Order emails successfully dispatched for {$order->order_number} to admin ({$alertEmail}) and customer ({$user->email})");
            } catch (\Throwable $e) {
                Log::warning("Failed to dispatch order emails for {$order->order_number}: " . $e->getMessage());
            }

            return $order;
        });
    }
}
