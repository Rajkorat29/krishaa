@extends('layouts.app')

@section('title', 'Checkout — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Checkout
  </p>

  <h1 style="font-size:clamp(2rem,4vw,3.2rem);font-weight:800;letter-spacing:-.03em;margin-bottom:1.8rem">
    Checkout
  </h1>

  @if($errors->any())
    <div style="background:var(--pink);color:var(--on-strong);padding:.9rem 1.4rem;border-radius:14px;margin-bottom:1.5rem;font-weight:500">
      <ul style="margin:0;padding-left:1.2rem">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
    @csrf

    <div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:2rem;align-items:start">
      <!-- Left: Delivery Address & Payment Method -->
      <div style="display:grid;gap:1.5rem">
        <!-- 1. Delivery Address Card -->
        <div class="page-box" style="margin-bottom:0">
          <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1rem">1. Delivery Address</h2>

          @if($addresses->count() > 0)
            <div style="display:grid;gap:.8rem;margin-bottom:1.2rem">
              @foreach($addresses as $addr)
                <label class="check" style="border:2px solid {{ $loop->first ? 'var(--pink)' : 'var(--line)' }};border-radius:14px;padding:1rem;background:var(--bg)">
                  <input type="radio" name="selected_address_id" value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }} onchange="toggleAddressFields(this.value === 'new')">
                  <div>
                    <strong>{{ $addr->name }}</strong> · <span class="mono">{{ $addr->phone }}</span>
                    <p style="margin:.25rem 0 0;color:var(--muted);font-size:.92rem">
                      {{ $addr->address_line }}, {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}
                    </p>
                  </div>
                </label>
              @endforeach

              <label class="check" style="border:2px dashed var(--line);border-radius:14px;padding:1rem;background:var(--bg)">
                <input type="radio" name="selected_address_id" value="new" onchange="toggleAddressFields(this.value === 'new')">
                <strong>Deliver to a new address</strong>
              </label>
            </div>
          @else
            <input type="hidden" name="selected_address_id" value="new">
          @endif

          <!-- New Address Fields -->
          <div id="newAddressFields" style="{{ $addresses->count() > 0 ? 'display:none;' : '' }}">
            <div class="form">
              <div class="fld">
                <label for="cName">Full Name *</label>
                <input class="in" type="text" id="cName" name="name" value="{{ old('name', auth()->user()?->name) }}" placeholder="e.g. Aarav Sharma">
              </div>

              <div class="row2">
                <div class="fld">
                  <label for="cPhone">Mobile Number (10 digits) *</label>
                  <input class="in" type="tel" id="cPhone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" maxlength="10" placeholder="9876543210">
                </div>
                <div class="fld">
                  <label for="cPincode">Pincode (6 digits) *</label>
                  <input class="in" type="text" id="cPincode" name="pincode" value="{{ old('pincode') }}" maxlength="6" placeholder="400001">
                </div>
              </div>

              <div class="fld">
                <label for="cAddress">Street Address / Apartment *</label>
                <textarea class="in" id="cAddress" name="address_line" rows="2" placeholder="House/Flat no, building, street, area">{{ old('address_line') }}</textarea>
              </div>

              <div class="row2">
                <div class="fld">
                  <label for="cCity">City *</label>
                  <input class="in" type="text" id="cCity" name="city" value="{{ old('city', 'Mumbai') }}">
                </div>
                <div class="fld">
                  <label for="cState">State *</label>
                  <input class="in" type="text" id="cState" name="state" value="{{ old('state', 'Maharashtra') }}">
                </div>
              </div>

              <label class="check" style="margin-top:.4rem">
                <input type="checkbox" name="save_address" value="1" checked> Save this address for future purchases
              </label>
            </div>
          </div>
        </div>

        <!-- 2. Payment Method Card -->
        <div class="page-box" style="margin-bottom:0">
          <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1rem">2. Payment Method</h2>

          <div style="display:grid;gap:.8rem">
            <label class="check" style="border:2px solid var(--line);border-radius:14px;padding:1rem;background:var(--bg)">
              <input type="radio" name="payment_method" value="COD" checked>
              <div>
                <strong>Cash on Delivery (COD)</strong>
                <p style="margin:.2rem 0 0;color:var(--muted);font-size:.88rem">Pay upon delivery with cash or UPI QR scan.</p>
              </div>
            </label>

            <label class="check" style="border:2px solid var(--line);border-radius:14px;padding:1rem;background:var(--bg)">
              <input type="radio" name="payment_method" value="UPI">
              <div>
                <strong>Instant UPI / QR</strong>
                <p style="margin:.2rem 0 0;color:var(--muted);font-size:.88rem">Google Pay, PhonePe, Paytm, or BHIM.</p>
              </div>
            </label>

            <label class="check" style="border:2px solid var(--line);border-radius:14px;padding:1rem;background:var(--bg)">
              <input type="radio" name="payment_method" value="Card">
              <div>
                <strong>Debit / Credit Card (Razorpay Test Mode)</strong>
                <p style="margin:.2rem 0 0;color:var(--muted);font-size:.88rem">Visa, Mastercard, RuPay with test payment simulation.</p>
              </div>
            </label>
          </div>

          <div class="fld" style="margin-top:1.2rem">
            <label for="orderNotes">Order notes (Optional)</label>
            <input class="in" type="text" id="orderNotes" name="notes" placeholder="Gift message or delivery instructions">
          </div>
        </div>
      </div>

      <!-- Right: Order Summary Sidebar -->
      <div class="page-box" style="position:sticky;top:6rem">
        <h2 style="font-size:1.4rem;font-weight:700;margin-bottom:1rem">Order Summary</h2>

        <div style="display:grid;gap:.8rem;max-height:280px;overflow-y:auto;margin-bottom:1.2rem;padding-right:.4rem">
          @foreach($totals['items'] as $item)
            @php
                $p = $item->product;
                $cover = $p->coverImage();
            @endphp
            <div style="display:flex;align-items:center;gap:.8rem;padding-bottom:.8rem;border-bottom:1px solid var(--line)">
              <div class="pimg card {{ $p->color_class }}" style="width:3.8rem;height:3.8rem;border-radius:10px;flex:none">
                @if($cover)
                  <img src="{{ asset('storage/' . $cover) }}" alt="{{ $p->name }}">
                @else
                  <svg class="art" aria-hidden="true"><use href="#{{ $p->art_symbol }}"/></svg>
                @endif
              </div>

              <div style="flex:1;min-width:0">
                <h4 style="font-size:.95rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  {{ $p->name }}
                </h4>
                <p class="mono" style="margin:.1rem 0 0;color:var(--muted);font-size:.78rem">
                  Size {{ $item->size_label }} · Qty {{ $item->quantity }}
                </p>
                <p style="margin:.2rem 0 0;font-weight:700;font-size:.95rem">
                  {{ \App\Models\Product::formatInr($p->price * $item->quantity) }}
                </p>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Summary Totals -->
        <div class="summary" style="border:0;padding:0;background:transparent">
          <div class="r">
            <span>Subtotal ({{ $totals['count'] }} item{{ $totals['count'] == 1 ? '' : 's' }})</span>
            <span>{{ $totals['formatted_subtotal'] }}</span>
          </div>

          @if($totals['coupon_discount'] > 0)
            <div class="r">
              <span>Coupon discount ({{ $totals['coupon']->code }})</span>
              <span style="color:var(--emerald)">− {{ $totals['formatted_coupon_discount'] }}</span>
            </div>
          @endif

          <div class="r">
            <span>Delivery fee</span>
            <span style="color:var(--emerald)">{{ $totals['formatted_delivery'] }}</span>
          </div>

          <div class="r t" style="border-top:1px dashed var(--line);margin-top:.4rem;padding-top:.8rem;font-size:1.25rem">
            <span>Total to pay</span>
            <span>{{ $totals['formatted_total'] }}</span>
          </div>

          @if($totals['total_savings'] > 0)
            <p class="save" style="margin-top:.4rem">
              You will save {{ $totals['formatted_savings'] }} on this purchase!
            </p>
          @endif
        </div>

        <button type="submit" class="btn solid" style="width:100%;justify-content:center;padding:1.1rem;margin-top:1.4rem;font-size:1.05rem">
          Place order · {{ $totals['formatted_total'] }}
        </button>
      </div>
    </div>
  </form>
</main>
@endsection

@push('scripts')
<script>
function toggleAddressFields(showNew) {
    const fields = document.getElementById('newAddressFields');
    if (fields) {
        fields.style.display = showNew ? 'block' : 'none';
    }
}
</script>
@endpush
