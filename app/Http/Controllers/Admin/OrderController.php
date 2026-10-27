<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('order_status', $status);
        }

        if ($payment = $request->input('payment')) {
            $query->where('payment_status', $payment);
        }

        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $orders = $query->paginate(15);
        $statuses = ['placed', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];
        $payments = ['pending', 'paid', 'failed', 'refunded'];

        return view('admin.orders.index', compact('orders', 'statuses', 'payments'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user']);
        $statuses = ['placed', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];
        $payments = ['pending', 'paid', 'failed', 'refunded'];

        return view('admin.orders.show', compact('order', 'statuses', 'payments'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:placed,confirmed,packed,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update($validated);
        return back()->with('success', "Order {$order->order_number} status updated.");
    }

    public function addNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $currentNotes = $order->notes ? $order->notes . "\n" : '';
        $timestamp = now()->format('d M Y, h:i A');
        $order->notes = $currentNotes . "[{$timestamp}] " . $validated['note'];
        $order->save();

        return back()->with('success', 'Internal note added.');
    }

    public function invoice(Order $order): View
    {
        $order->load(['items.product', 'user']);
        return view('admin.orders.invoice', compact('order'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $fileName = 'orders_export_' . date('Y_m_d_His') . '.csv';
        $orders = Order::with('items')->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order Number', 'Date', 'Customer', 'Phone', 'Email', 'Items Qty', 'Subtotal', 'Discount', 'Total', 'Payment Method', 'Payment Status', 'Order Status', 'Address']);

            foreach ($orders as $o) {
                fputcsv($handle, [
                    $o->order_number,
                    $o->created_at->format('Y-m-d H:i'),
                    $o->customer_name,
                    $o->customer_phone,
                    $o->customer_email,
                    $o->items->sum('quantity'),
                    $o->subtotal,
                    $o->discount,
                    $o->total,
                    $o->payment_method,
                    $o->payment_status,
                    $o->order_status,
                    "{$o->shipping_address_line}, {$o->shipping_city}, {$o->shipping_state} - {$o->shipping_pincode}",
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
