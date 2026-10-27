<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSales = (float) Order::whereNotIn('order_status', ['cancelled'])->sum('total');
        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $pendingOrders = Order::whereIn('order_status', ['placed'])->count();
        $customersCount = User::where('role', 'customer')->count();
        $lowStockProducts = Product::where('stock', '<=', 5)->where('is_active', true)->take(6)->get();

        // 30-Day Sales Data for Chart.js
        $chartLabels = [];
        $chartValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $dailyTotal = (float) Order::whereDate('created_at', $date)
                ->whereNotIn('order_status', ['cancelled'])
                ->sum('total');
            $chartValues[] = $dailyTotal;
        }

        $latestOrders = Order::latest()->take(10)->with('items')->get();
        $topProducts = Product::orderByDesc('rating_count')->take(5)->with(['category', 'metal'])->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'ordersToday',
            'pendingOrders',
            'customersCount',
            'lowStockProducts',
            'chartLabels',
            'chartValues',
            'latestOrders',
            'topProducts'
        ));
    }
}
