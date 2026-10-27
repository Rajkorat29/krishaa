@extends('layouts.app')

@section('title', ($category ? $category->name : 'All Jewellery') . ' — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main id="shopView" class="wrap shop" aria-labelledby="shopH">
  <p class="crumbs mono">
    <a href="{{ url('/') }}">Home</a> / {{ $category ? $category->name : 'Jewellery' }}
  </p>

  <div class="shop-top">
    <div>
      <h1 id="shopH">{{ $category ? $category->name : 'Jewellery Collection' }}</h1>
      <p class="muted" id="resultCount" role="status" style="margin-bottom:.4rem">
        Showing {{ $products->count() }} of {{ $totalCount }} pieces
      </p>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.6rem">
        <a href="{{ route('shop.index') }}" class="btn sm {{ !$category ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">All Pieces</a>
        <a href="{{ route('rings') }}" class="btn sm {{ optional($category)->slug === 'rings' ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">Rings</a>
        <a href="{{ route('earrings') }}" class="btn sm {{ optional($category)->slug === 'earrings' ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">Earrings</a>
        <a href="{{ route('bangles') }}" class="btn sm {{ optional($category)->slug === 'bangles' ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">Bangles</a>
        <a href="{{ route('necklaces') }}" class="btn sm {{ optional($category)->slug === 'necklaces' ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">Necklaces</a>
        <a href="{{ route('pearls') }}" class="btn sm {{ optional($category)->slug === 'pearls' ? 'solid' : '' }}" style="padding:.35rem .9rem;font-size:.82rem">Pearls</a>
      </div>
    </div>

    <div class="shop-tools">
      <label class="search">
        <span class="sr">Search pieces</span>
        <svg aria-hidden="true"><use href="#search"/></svg>
        <input id="q" type="search" placeholder="Search pieces, metals, stones" value="{{ request('q') }}" autocomplete="off">
      </label>

      <label>
        <span class="sr">Sort by</span>
        <select class="in" id="sort">
          <option value="pop" {{ request('sort') == 'pop' ? 'selected' : '' }}>Popularity</option>
          <option value="low" {{ request('sort') == 'low' ? 'selected' : '' }}>Price: low to high</option>
          <option value="high" {{ request('sort') == 'high' ? 'selected' : '' }}>Price: high to low</option>
          <option value="rate" {{ request('sort') == 'rate' ? 'selected' : '' }}>Customer rating</option>
          <option value="new" {{ request('sort') == 'new' ? 'selected' : '' }}>Newest first</option>
        </select>
      </label>

      <button class="btn filter-btn" id="filterBtn" type="button" aria-expanded="false" aria-controls="filters">
        Filters
      </button>

      @auth
        @if(auth()->user()->isAdmin())
          <a class="btn solid" href="{{ route('admin.products.create') }}">
            <svg class="ic" aria-hidden="true"><use href="#plus"/></svg>Add new piece
          </a>
        @endif
      @endauth
    </div>
  </div>

  <div class="shop-body">
    <!-- Filter Sidebar -->
    <aside class="filters" id="filters" aria-label="Filters">
      <div>
        <h3>Max price</h3>
        <input type="range" id="priceMax" min="1000" max="120000" step="1000" value="{{ request('max_price', 120000) }}" aria-label="Maximum price">
        <div class="mono" id="priceLbl">Up to {{ \App\Models\Product::formatInr(request('max_price', 120000)) }}</div>
      </div>

      <fieldset id="metalF">
        <legend>Metal</legend>
        @php
            $selectedMetals = explode(',', request('metals', ''));
        @endphp
        @foreach($metals as $metal)
          <label class="check">
            <input type="checkbox" value="{{ $metal->name }}" {{ in_array($metal->name, $selectedMetals) ? 'checked' : '' }}>
            {{ $metal->name }}
          </label>
        @endforeach
      </fieldset>

      <fieldset>
        <legend>Customer rating</legend>
        <label class="check">
          <input type="radio" name="rate" value="0" {{ !request('rating') ? 'checked' : '' }}> Any
        </label>
        <label class="check">
          <input type="radio" name="rate" value="4" {{ request('rating') == '4' ? 'checked' : '' }}> 4★ &amp; above
        </label>
        <label class="check">
          <input type="radio" name="rate" value="4.5" {{ request('rating') == '4.5' ? 'checked' : '' }}> 4.5★ &amp; above
        </label>
      </fieldset>

      <fieldset>
        <legend>Show only</legend>
        <label class="check">
          <input type="checkbox" id="wishOnly" {{ request('wish_only') ? 'checked' : '' }}> My wishlist
        </label>
        <label class="check">
          <input type="checkbox" id="inStock" {{ request('in_stock') ? 'checked' : '' }}> In stock only
        </label>
      </fieldset>

      <button class="btn" id="clearF" type="button">Clear filters</button>
    </aside>

    <!-- Product Grid -->
    <section aria-label="Product results" style="min-width:0">
      <div class="grid" id="grid">
        @foreach($products as $product)
          @include('partials.product-card', ['product' => $product, 'wishedProductIds' => $wishedProductIds])
        @endforeach
      </div>

      <div class="empty" id="empty" {{ $products->count() > 0 ? 'hidden' : '' }}>
        <h3>No pieces match these filters</h3>
        <p class="muted">Try adjusting your price range, choosing another metal, or clearing the filters.</p>
      </div>
    </section>
  </div>
</main>
@endsection

@push('scripts')
  @vite(['resources/js/shop.js'])
@endpush
