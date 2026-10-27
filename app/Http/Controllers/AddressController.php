<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()->latest()->get();
        return view('pages.account.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'address_line' => 'required|string|min:8|max:500',
            'city' => 'required|string|max:80',
            'state' => 'required|string|max:80',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();

        if ($request->boolean('is_default')) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        Address::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'pincode' => $validated['pincode'],
            'address_line' => $validated['address_line'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'is_default' => $request->boolean('is_default') || $user->addresses()->count() === 0,
        ]);

        return back()->with('success', 'Address saved successfully.');
    }

    public function destroy(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();
        return back()->with('success', 'Address removed.');
    }
}
