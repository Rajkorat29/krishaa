@if($totals['count'] === 0)
  <div class="empty">
    <h3>Your bag is empty</h3>
    <p class="muted">Add a jewellery piece you like and it will show up here.</p>
    <a class="btn solid" href="{{ route('rings') }}" style="justify-content:center;margin-top:1rem">Browse rings</a>
  </div>
@else
  <div class="citems">
    @foreach($totals['items'] as $item)
      @php
          $prod = $item->product;
          $cover = $prod->coverImage();
          $ringSizes = ['10', '12', '14', '16', '18', '20', '22', '24'];
      @endphp
      <div class="citem" data-item-id="{{ $item->id }}" data-qty="{{ $item->quantity }}">
        <div class="pimg card {{ $prod->color_class }}">
          @if($cover)
            <img src="{{ asset('storage/' . $cover) }}" alt="{{ $prod->name }}">
          @else
            <svg class="art" aria-hidden="true"><use href="#{{ $prod->art_symbol }}"/></svg>
          @endif
        </div>

        <div style="min-width:0">
          <h3>{{ $prod->name }}</h3>
          <p class="pmeta" style="margin:.1rem 0 0">{{ $prod->metal->name ?? '' }}</p>

          <p class="pprice" style="margin:.3rem 0 0">
            <b>{{ $prod->formatted_price }}</b>
            @if($prod->mrp && $prod->mrp > $prod->price)
              <s>{{ $prod->formatted_mrp }}</s>
              <span class="off">{{ $prod->discount_percentage }}% off</span>
            @endif
          </p>

          <div class="ctl">
            @if($prod->category && $prod->category->slug === 'rings')
              <select class="in csize" data-cart-act="size" aria-label="Ring size">
                @foreach($ringSizes as $s)
                  <option value="{{ $s }}" {{ $item->size_label == $s ? 'selected' : '' }}>Size {{ $s }}</option>
                @endforeach
              </select>
            @endif

            <span class="stepper">
              <button type="button" data-cart-act="minus" aria-label="Decrease quantity">−</button>
              <span aria-live="polite">{{ $item->quantity }}</span>
              <button type="button" data-cart-act="plus" aria-label="Increase quantity">+</button>
            </span>

            <button class="linkbtn" type="button" data-cart-act="remove">Remove</button>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Coupon section --}}
  <div style="background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:.9rem">
    @if($totals['coupon'])
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <span class="badge success">Coupon applied</span>
          <b style="margin-left:.5rem;font-family:var(--f-mono)">{{ $totals['coupon']->code }}</b>
          <p class="muted" style="margin:.2rem 0 0;font-size:.85rem">You saved {{ $totals['formatted_coupon_discount'] }}!</p>
        </div>
        <button type="button" class="linkbtn" id="removeCouponBtn" style="color:var(--pink)">Remove</button>
      </div>
    @else
      <form id="couponForm" style="display:flex;gap:.5rem" novalidate>
        <input class="in" type="text" name="coupon_code" placeholder="Enter coupon (e.g. WELCOME10)" style="text-transform:uppercase" autocomplete="off" required>
        <button class="btn" type="submit" style="flex:none">Apply</button>
      </form>
    @endif
  </div>

  {{-- Price Summary --}}
  <div class="summary">
    <div class="lbl" style="margin:0">Price details</div>
    <div class="r">
      <span>Price ({{ $totals['count'] }} item{{ $totals['count'] == 1 ? '' : 's' }})</span>
      <span>{{ $totals['formatted_mrp'] }}</span>
    </div>
    @if($totals['catalog_discount'] > 0)
      <div class="r">
        <span>Catalog discount</span>
        <span style="color:var(--emerald)">− {{ \App\Models\Product::formatInr($totals['catalog_discount']) }}</span>
      </div>
    @endif
    @if($totals['coupon_discount'] > 0)
      <div class="r">
        <span>Coupon discount</span>
        <span style="color:var(--emerald)">− {{ $totals['formatted_coupon_discount'] }}</span>
      </div>
    @endif
    <div class="r">
      <span>Delivery charges</span>
      <span style="color:var(--emerald)">{{ $totals['formatted_delivery'] }}</span>
    </div>
    <div class="r t">
      <span>Total amount</span>
      <span>{{ $totals['formatted_total'] }}</span>
    </div>
    @if($totals['total_savings'] > 0)
      <p class="save">You will save {{ $totals['formatted_savings'] }} on this order</p>
    @endif
  </div>

  <a class="btn solid" href="{{ route('checkout.index') }}" style="justify-content:center;padding:1rem">
    Proceed to checkout
  </a>
@endif
