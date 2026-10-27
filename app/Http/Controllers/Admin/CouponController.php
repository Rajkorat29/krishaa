<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:coupons,code',
            'type' => 'required|in:percent,flat',
            'value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = (float) $request->input('min_order', 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        Coupon::create($validated);
        return back()->with('success', "Coupon '{$validated['code']}' created.");
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => "required|string|max:30|unique:coupons,code,{$coupon->id}",
            'type' => 'required|in:percent,flat',
            'value' => 'required|numeric|min:1',
            'min_order' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order'] = (float) $request->input('min_order', 0);
        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);
        return back()->with('success', "Coupon '{$coupon->code}' updated.");
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
