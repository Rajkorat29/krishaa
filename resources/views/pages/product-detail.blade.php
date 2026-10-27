@extends('layouts.app')

@section('title', $product->name . ' — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> /
    <a href="{{ $product->category ? route('shop.category', $product->category->slug) : route('rings') }}">
      {{ $product->category ? $product->category->name : 'Jewellery' }}
    </a> /
    {{ $product->name }}
  </p>

  <div class="page-box">
    @php
        $images = $product->images;
        $cover = $product->coverImage();
        $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];
        $isWished = in_array($product->id, $wishedProductIds ?? []);
    @endphp

    <div class="d-grid">
      <!-- Media Gallery -->
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

      <!-- Product Info -->
      <div class="d-info">
        <p class="lbl" style="margin:0">{{ $product->category->name ?? 'Jewellery' }} · {{ $product->metal->name ?? '' }}</p>
        <h1 style="font-size:clamp(2rem,3.8vw,3rem);font-weight:800;letter-spacing:-.03em">{{ $product->name }}</h1>

        @if($product->rating_count > 0)
          <p style="margin:0">
            <span class="chip-rate">
              <svg aria-hidden="true"><use href="#star"/></svg>{{ number_format($product->rating_avg, 1) }}
            </span>
            <span class="muted">{{ number_format($product->rating_count) }} customer ratings</span>
          </p>
        @else
          <p style="margin:0"><span class="chip-rate" style="background:var(--gold);color:var(--ink)">New piece</span></p>
        @endif

        <p class="pprice big" style="margin:0">
          <b>{{ $product->formatted_price }}</b>
          @if($product->mrp && $product->mrp > $product->price)
            <s>{{ $product->formatted_mrp }}</s>
            <span class="off">{{ $product->discount_percentage }}% off</span>
          @endif
        </p>

        <p class="muted" style="margin:0;font-size:.9rem">Inclusive of all taxes. Free doorstep insured delivery. 7-day hassle-free returns.</p>

        <!-- Indian Ring Size Selector -->
        @if($product->category && $product->category->slug === 'rings')
          <div>
            <div class="lbl">Ring size (India Standard)</div>
            <div class="sizes" role="radiogroup" aria-label="Ring size" id="sizeGroup">
              @foreach($ringSizes as $s)
                <button class="sz" type="button" role="radio" data-size="{{ $s }}" aria-checked="{{ $s === '16' ? 'true' : 'false' }}">
                  {{ $s }}
                </button>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Quantity & Actions -->
        <div class="d-actions" style="margin-top:.5rem">
          <button class="btn solid" type="button" id="standaloneAddBag" style="padding:1rem 1.8rem">
            Add to bag
          </button>
          <button class="btn" type="button" id="standaloneBuyNow" style="padding:1rem 1.8rem">
            Buy now
          </button>
          <button class="btn {{ $isWished ? 'active' : '' }}" type="button" data-wish-product="{{ $product->id }}" aria-pressed="{{ $isWished ? 'true' : 'false' }}">
            {{ $isWished ? 'Saved' : 'Wishlist' }}
          </button>
        </div>

        <!-- Pincode Check -->
        <div style="margin-top:.8rem">
          <div class="lbl">Check delivery date</div>
          <div class="deliv">
            <input class="in" id="pin" inputmode="numeric" maxlength="6" placeholder="Enter 6-digit pincode" aria-label="Pincode">
            <button class="btn" type="button" id="pinCheckBtn">Check</button>
          </div>
          <p class="pinmsg" id="pinMsg" role="status"></p>
        </div>

        <!-- Specs List -->
        <dl class="specs" style="margin-top:.8rem">
          <dt>Metal</dt>
          <dd>{{ $product->metal->name ?? 'Pure Gold' }}</dd>

          @if($product->weight_grams > 0)
            <dt>Weight</dt>
            <dd>{{ $product->weight_grams }} g</dd>
          @endif

          @if($product->stone)
            <dt>Stone</dt>
            <dd>{{ $product->stone }}</dd>
          @endif

          <dt>SKU</dt>
          <dd class="mono">{{ $product->sku }}</dd>

          <dt>Certificate</dt>
          <dd>BIS hallmark on gold, 925 authenticity stamp on silver</dd>
        </dl>

        @if($product->description)
          <div class="content-text" style="margin-top:1rem;border-top:1px solid var(--line);padding-top:1rem">
            <p>{{ $product->description }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Customer Reviews Section -->
  <section class="page-box" id="reviews">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
      <div>
        <h2 style="font-size:1.8rem;font-weight:800">Customer Reviews</h2>
        <p class="muted" style="margin:.3rem 0 0">
          Average {{ number_format($product->rating_avg, 1) }} out of 5 stars based on {{ $product->approvedReviews->count() }} reviews
        </p>
      </div>

      @auth
        <a class="btn solid" href="#writeReview">Write a review</a>
      @else
        <a class="btn" href="{{ route('login') }}">Sign in to write a review</a>
      @endauth
    </div>

    @if($product->approvedReviews->count() === 0)
      <div class="empty">
        <h3>No reviews yet</h3>
        <p class="muted">Be the first customer to share your thoughts on this handcrafted piece!</p>
      </div>
    @else
      <div style="display:grid;gap:1rem">
        @foreach($product->approvedReviews as $review)
          <div style="border-bottom:1px solid var(--line);padding-bottom:1.2rem">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.3rem">
              <span class="chip-rate" style="padding:.1rem .5rem;font-size:.8rem">
                <svg aria-hidden="true"><use href="#star"/></svg>{{ $review->rating }}
              </span>
              <strong style="font-size:1rem">{{ $review->title ?: 'Verified Purchase' }}</strong>
            </div>
            <p style="margin:0 0 .4rem;color:var(--fg)">{{ $review->body }}</p>
            <p class="mono" style="margin:0;color:var(--muted);font-size:.78rem">
              By {{ $review->user->name ?? 'Customer' }} · {{ $review->created_at->format('d M Y') }}
            </p>
          </div>
        @endforeach
      </div>
    @endif

    <!-- Review Submission Form -->
    @auth
      <div id="writeReview" style="margin-top:2.5rem;border-top:1px solid var(--line);padding-top:2rem">
        <h3 style="font-size:1.4rem;font-weight:700;margin-bottom:1rem">Leave your review</h3>
        <form action="{{ route('reviews.store') }}" method="POST" class="form">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">

          <div class="fld">
            <label for="reviewRating">Rating</label>
            <select name="rating" id="reviewRating" class="in" style="max-width:200px" required>
              <option value="5">★★★★★ (5 Stars - Excellent)</option>
              <option value="4">★★★★☆ (4 Stars - Very Good)</option>
              <option value="3">★★★☆☆ (3 Stars - Good)</option>
              <option value="2">★★☆☆☆ (2 Stars - Fair)</option>
              <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
            </select>
          </div>

          <div class="fld">
            <label for="reviewTitle">Review Title (Optional)</label>
            <input type="text" name="title" id="reviewTitle" class="in" placeholder="Summary of your experience">
          </div>

          <div class="fld">
            <label for="reviewBody">Your Review</label>
            <textarea name="body" id="reviewBody" rows="4" class="in" placeholder="Share details about design, finish, weight and occasion..." required></textarea>
          </div>

          <button type="submit" class="btn solid" style="justify-self:start">Submit review</button>
        </form>
      </div>
    @endauth
  </section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const prodId = {{ $product->id }};
    let selectedSize = '16';

    // Size selector
    document.querySelectorAll('#sizeGroup .sz').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#sizeGroup .sz').forEach(b => b.setAttribute('aria-checked', 'false'));
            btn.setAttribute('aria-checked', 'true');
            selectedSize = btn.dataset.size;
        });
    });

    // Thumbnails
    document.querySelectorAll('.d-thumbs button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.d-thumbs button').forEach(b => b.removeAttribute('aria-current'));
            btn.setAttribute('aria-current', 'true');
            const mainImg = document.getElementById('dMainImg');
            if (mainImg) mainImg.src = btn.dataset.thumbSrc;
        });
    });

    // Add to bag
    const addBtn = document.getElementById('standaloneAddBag');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            window.DaisyCart.add(prodId, selectedSize, 1, true);
        });
    }

    // Buy now
    const buyBtn = document.getElementById('standaloneBuyNow');
    if (buyBtn) {
        buyBtn.addEventListener('click', async () => {
            await window.DaisyCart.add(prodId, selectedSize, 1, false);
            window.location.href = '{{ route("checkout.index") }}';
        });
    }

    // Pincode check
    const pinBtn = document.getElementById('pinCheckBtn');
    const pinInput = document.getElementById('pin');
    const pinMsg = document.getElementById('pinMsg');
    if (pinBtn && pinInput && pinMsg) {
        pinBtn.addEventListener('click', () => {
            const v = pinInput.value.trim();
            if (/^[1-9][0-9]{5}$/.test(v)) {
                const est = new Date();
                est.setDate(est.getDate() + 4);
                const dt = est.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' });
                pinMsg.className = 'pinmsg';
                pinMsg.textContent = `Estimated delivery by ${dt}. Free insured delivery.`;
            } else {
                pinMsg.className = 'pinmsg bad';
                pinMsg.textContent = 'Enter a valid 6-digit Indian postal pincode.';
            }
        });
    }
});
</script>
@endpush
