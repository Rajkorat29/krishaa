<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')->withCount('orders')->latest();

        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $query->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function toggleBlock(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot block an administrator.');
        }

        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        $action = $user->is_blocked ? 'blocked' : 'unblocked';
        return back()->with('success', "Customer {$user->name} has been {$action}.");
    }
}
