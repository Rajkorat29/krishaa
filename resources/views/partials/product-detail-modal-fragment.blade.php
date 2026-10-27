@php
    $images = $product->images;
    $cover = $product->coverImage();
    $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];
    $isWished = in_array($product->id, $wishedProductIds ?? []);
@endphp

<div class="d-grid">
  <div>
    <div class="pimg d-main card {{ $product->color_class }}">
      @if($cover)
        <img id="dMainImg" src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}">
      @else
        <svg class="art" aria-hidden="true"><use href="#{{ $product->art_symbol }}"/></svg>
      @endif
    </div>

    @if($images->count() > 1)
      <div class="d-thumbs">
        @foreach($images as $idx => $img)
          <button type="button" data-thumb-src="{{ asset('storage/' . $img->path) }}" aria-label="Photo {{ $idx + 1 }}" {{ $idx === 0 ? 'aria-current=true' : '' }}>
            <img src="{{ asset('storage/' . ($img->thumb_path ?? $img->path)) }}" alt="">
          </button>
        @endforeach
      </div>
    @endif
  </div>

  <div class="d-info">
    <p class="lbl" style="margin:0">{{ $product->category->name ?? 'Jewellery' }} · {{ $product->metal->name ?? '' }}</p>
    <h2 id="dName">{{ $product->name }}</h2>

    @if($product->rating_count > 0)
      <p style="margin:0">
        <span class="chip-rate">
          <svg aria-hidden="true"><use href="#star"/></svg>{{ number_format($product->rating_avg, 1) }}
        </span>
        <span class="muted">{{ number_format($product->rating_count) }} ratings</span>
      </p>
    @else
      <p style="margin:0"><span class="chip-rate" style="background:var(--gold);color:var(--ink)">New</span></p>
    @endif

    <p class="pprice big" style="margin:0">
      <b>{{ $product->formatted_price }}</b>
      @if($product->mrp && $product->mrp > $product->price)
        <s>{{ $product->formatted_mrp }}</s>
        <span class="off">{{ $product->discount_percentage }}% off</span>
      @endif
    </p>

    <p class="muted" style="margin:0;font-size:.9rem">Inclusive of all taxes. Free delivery. 7-day easy returns.</p>

    @if($product->category && $product->category->slug === 'rings')
      <div>
        <div class="lbl">Ring size (India)</div>
        <div class="sizes" role="radiogroup" aria-label="Ring size">
          @foreach($ringSizes as $s)
            <button class="sz" type="button" role="radio" data-size="{{ $s }}" aria-checked="{{ $s === '16' ? 'true' : 'false' }}">
              {{ $s }}
            </button>
          @endforeach
        </div>
      </div>
    @endif

    <div class="d-actions">
      <button class="btn solid" type="button" data-modal-act="add" data-product-id="{{ $product->id }}">Add to bag</button>
      <button class="btn" type="button" data-modal-act="buy" data-product-id="{{ $product->id }}">Buy now</button>
      <button class="btn {{ $isWished ? 'active' : '' }}" type="button" data-wish-product="{{ $product->id }}" aria-pressed="{{ $isWished ? 'true' : 'false' }}">
        {{ $isWished ? 'Saved' : 'Wishlist' }}
      </button>
    </div>

    <div>
      <div class="lbl">Delivery</div>
      <div class="deliv">
        <input class="in" id="pin" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit pincode" aria-label="Pincode">
        <button class="btn" type="button" data-d="pin">Check</button>
      </div>
      <p class="pinmsg" id="pinMsg" role="status"></p>
    </div>

    <dl class="specs">
      <dt>Metal</dt>
      <dd>{{ $product->metal->name ?? 'Gold' }}</dd>

      @if($product->weight_grams > 0)
        <dt>Weight</dt>
        <dd>{{ $product->weight_grams }} g</dd>
      @endif

      @if($product->stone)
        <dt>Stone</dt>
        <dd>{{ $product->stone }}</dd>
      @endif

      <dt>Certificate</dt>
      <dd>BIS hallmark on gold, 925 stamp on silver</dd>
    </dl>

    @if($product->short_description)
      <p style="margin:0">{{ $product->short_description }}</p>
    @endif

    <div style="margin-top:.5rem">
      <a href="{{ route('product.show', $product->slug) }}" class="linkbtn" style="font-weight:600">
        View full specs &amp; customer reviews →
      </a>
    </div>
  </div>
</div>
