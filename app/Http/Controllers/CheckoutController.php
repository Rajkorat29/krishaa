<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request);
        $totals = $this->cartService->getTotals($cart);

        if ($totals['count'] === 0) {
            return redirect()->route('rings')->with('error', 'Your shopping bag is empty.');
        }

        $addresses = $request->user()->addresses()->latest()->get();

        return view('pages.checkout', compact('cart', 'totals', 'addresses'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $cart = $this->cartService->getCart($request);

        $selectedAddressId = $request->input('selected_address_id');

        if ($selectedAddressId && $selectedAddressId !== 'new') {
            $address = Address::where('id', $selectedAddressId)->where('user_id', $user->id)->firstOrFail();
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'pincode' => $address->pincode,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'state' => $address->state,
            ];
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
                'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
                'address_line' => 'required|string|min:8|max:500',
                'city' => 'required|string|max:80',
                'state' => 'required|string|max:80',
                'save_address' => 'nullable|boolean',
            ]);

            $addressData = $validated;

            if ($request->boolean('save_address')) {
                Address::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'pincode' => $validated['pincode'],
                    'address_line' => $validated['address_line'],
                    'city' => $validated['city'],
                    'state' => $validated['state'],
                    'is_default' => $user->addresses()->count() === 0,
                ]);
            }
        }

        $paymentMethod = $request->input('payment_method', 'COD');
        if (! in_array($paymentMethod, ['COD', 'UPI', 'Card'])) {
            $paymentMethod = 'COD';
        }

        try {
            $order = $this->checkoutService->placeOrder(
                $user,
                $cart,
                $addressData,
                $paymentMethod,
                $request->input('notes')
            );

            return redirect()->route('checkout.confirmation', $order->order_number);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function confirmation(Request $request, string $orderNumber): View
    {
        $user = $request->user();
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        // Ensure user owns order or is admin
        if ($order->user_id && (! $user || ($order->user_id !== $user->id && ! $user->isAdmin()))) {
            abort(403);
        }

        return view('pages.order-confirmation', compact('order'));
    }
}
