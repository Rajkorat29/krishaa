@extends('layouts.app')

@section('title', 'My Wishlist — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap shop" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Wishlist
  </p>

  <div class="shop-top">
    <div>
      <h1 id="shopH">My Wishlist</h1>
      <p class="muted">
        {{ $products->count() }} {{ $products->count() === 1 ? 'piece' : 'pieces' }} saved
      </p>
    </div>

    <div>
      <a class="btn solid" href="{{ route('rings') }}">Browse rings</a>
    </div>
  </div>

  @if($products->count() === 0)
    <div class="empty" style="margin-top:2rem">
      <h3>Your wishlist is empty</h3>
      <p class="muted">Explore our collections and tap the heart icon on any piece you love.</p>
      <a class="btn solid" href="{{ route('rings') }}" style="margin-top:1rem">Explore rings</a>
    </div>
  @else
    <div class="grid" style="margin-top:2rem">
      @foreach($products as $product)
        @include('partials.product-card', ['product' => $product, 'wishedProductIds' => $wishedProductIds])
      @endforeach
    </div>
  @endif
</main>
@endsection
