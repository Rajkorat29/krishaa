<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = auth()->user();
            $cartCount = 0;
            $wishlistCount = 0;
            $wishedProductIds = [];

            if ($user) {
                $cart = Cart::where('user_id', $user->id)->with('items')->first();
                $cartCount = $cart ? $cart->items->sum('quantity') : 0;
                $wishedProductIds = Wishlist::where('user_id', $user->id)->pluck('product_id')->toArray();
                $wishlistCount = count($wishedProductIds);
            } else {
                $sessionToken = session()->get('cart_session_token');
                if ($sessionToken) {
                    $cart = Cart::where('session_token', $sessionToken)->with('items')->first();
                    $cartCount = $cart ? $cart->items->sum('quantity') : 0;
                }
                $wishedProductIds = session()->get('guest_wishlist', []);
                $wishlistCount = count($wishedProductIds);
            }

            $view->with([
                'cartCount' => $cartCount,
                'wishlistCount' => $wishlistCount,
                'wishedProductIds' => $wishedProductIds,
            ]);
        });
    }
}
