<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();
        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        $sessionToken = $request->session()->get('cart_session_token');
        $guestWishlist = $request->session()->get('guest_wishlist', []);

        $request->session()->regenerate();

        // Merge guest cart
        if ($sessionToken) {
            app(CartService::class)->mergeGuestCart($sessionToken, $user);
        }

        // Merge guest wishlist
        foreach ($guestWishlist as $pId) {
            Wishlist::firstOrCreate(['user_id' => $user->id, 'product_id' => $pId]);
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
