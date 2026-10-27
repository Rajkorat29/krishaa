<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->with('items')->get();
        return view('pages.account.orders', compact('orders'));
    }

    public function show(Request $request, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->firstOrFail();

        return view('pages.account.order-detail', compact('order'));
    }

    public function cancel(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! $order->canCancel()) {
            return back()->with('error', "Order {$order->order_number} cannot be cancelled as it is already {$order->order_status}.");
        }

        $order->order_status = 'cancelled';
        $order->save();

        return back()->with('success', "Order {$order->order_number} has been cancelled.");
    }
}
