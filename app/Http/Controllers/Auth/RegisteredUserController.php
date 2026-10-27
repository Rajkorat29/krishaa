<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'customer',
            'is_blocked' => false,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $sessionToken = $request->session()->get('cart_session_token');
        $guestWishlist = $request->session()->get('guest_wishlist', []);

        Auth::login($user);

        // Merge cart
        if ($sessionToken) {
            app(CartService::class)->mergeGuestCart($sessionToken, $user);
        }

        // Merge wishlist
        foreach ($guestWishlist as $pId) {
            Wishlist::firstOrCreate(['user_id' => $user->id, 'product_id' => $pId]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
