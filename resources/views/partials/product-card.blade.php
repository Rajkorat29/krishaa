@php
    $cover = $product->coverImage();
    $isWished = in_array($product->id, $wishedProductIds ?? []);
    $reviewsCount = $product->rating_count ?? 0;
    $kfmt = $reviewsCount >= 1000 ? round($reviewsCount / 1000, 1) . 'k' : $reviewsCount;
@endphp
<article class="pcard" data-id="{{ $product->id }}" data-category="{{ $product->category->slug ?? '' }}">
  <div class="pimg card {{ $product->color_class }}">
    @if($cover)
      <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" loading="lazy">
    @else
      <svg class="art" aria-hidden="true"><use href="#{{ $product->art_symbol }}"/></svg>
    @endif

    <button class="open-hit" type="button" data-open-detail="{{ $product->slug }}" aria-label="View {{ $product->name }}"></button>

    <button class="icon wish {{ $isWished ? 'active' : '' }}" type="button" data-wish-product="{{ $product->id }}" aria-pressed="{{ $isWished ? 'true' : 'false' }}" aria-label="{{ $isWished ? 'Remove from wishlist' : 'Add to wishlist' }}">
      <svg aria-hidden="true"><use href="#heart"/></svg>
    </button>

    @if($reviewsCount > 0)
      <span class="rate">
        <svg aria-hidden="true"><use href="#star"/></svg>{{ number_format($product->rating_avg, 1) }} <i>|</i> {{ $kfmt }}
      </span>
    @else
      <span class="rate new">New</span>
    @endif
  </div>

  <div class="pinfo">
    <h3>
      <button class="pname" type="button" data-open-detail="{{ $product->slug }}">
        {{ $product->name }}
      </button>
    </h3>

    <p class="pmeta">{{ $product->metal->name ?? '' }}{{ $product->weight_grams > 0 ? ' · ' . $product->weight_grams . ' g' : '' }}</p>

    <p class="pprice">
      <b>{{ $product->formatted_price }}</b>
      @if($product->mrp && $product->mrp > $product->price)
        <s>{{ $product->formatted_mrp }}</s>
        <span class="off">{{ $product->discount_percentage }}% off</span>
      @endif
    </p>

    <div class="pact">
      <button class="btn solid" type="button" data-add-bag="{{ $product->id }}" data-size="16">Add to bag</button>
      <a class="linkbtn" href="{{ route('product.show', $product->slug) }}">Details</a>
    </div>
  </div>
</article>
