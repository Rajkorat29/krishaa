@extends('layouts.app')

@section('title', 'Order ' . $order->order_number . ' — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> /
    <a href="{{ route('account.orders') }}">My Orders</a> /
    {{ $order->order_number }}
  </p>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h1 style="font-size:clamp(1.8rem,3.6vw,2.6rem);font-weight:800;letter-spacing:-.03em">
        Order {{ $order->order_number }}
      </h1>
      <p class="mono" style="color:var(--muted);margin:.3rem 0 0">
        Placed on {{ $order->created_at->format('d M Y, h:i A') }}
      </p>
    </div>

    @if($order->canCancel())
      <form action="{{ route('account.orders.cancel', $order->order_number) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
        @csrf
        <button type="submit" class="btn danger">Cancel order</button>
      </form>
    @endif
  </div>

  <div class="page-box">
    <!-- Status Timeline -->
    <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:.8rem">Delivery Timeline</h2>

    @if($order->order_status === 'cancelled')
      <div style="background:#fde8f0;color:#d81b66;padding:1rem 1.4rem;border-radius:14px;font-weight:600">
        This order was cancelled. If payment was deducted, it will be refunded within 3-5 business days.
      </div>
    @else
      @php
          $stages = ['placed', 'confirmed', 'packed', 'shipped', 'delivered'];
          $currentIdx = array_search($order->order_status, $stages);
          if ($currentIdx === false) $currentIdx = 0;
      @endphp
      <ul class="timeline">
        @foreach($stages as $idx => $stage)
          <li class="{{ $idx < $currentIdx ? 'done' : ($idx === $currentIdx ? 'current' : '') }}">
            @if($idx < $currentIdx) ✓ @endif
            {{ ucfirst($stage) }}
          </li>
        @endforeach
      </ul>
    @endif

    <!-- Items List -->
    <div style="border-top:1px solid var(--line);margin-top:1.5rem;padding-top:1.5rem">
      <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem">Items in this order</h3>

      <div style="display:grid;gap:1rem">
        @foreach($order->items as $item)
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;background:var(--bg);padding:1rem;border-radius:16px;border:1px solid var(--line)">
            <div>
              <h4 style="font-size:1.05rem;font-weight:700">{{ $item->product_name }}</h4>
              <p class="mono" style="margin:.2rem 0 0;color:var(--muted);font-size:.82rem">
                Size {{ $item->size_label }} · Qty {{ $item->quantity }} · {{ $item->formatted_unit_price }} each
              </p>
            </div>
            <b>{{ $item->formatted_subtotal }}</b>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Details Grid -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:2rem;border-top:1px solid var(--line);padding-top:1.5rem">
      <div>
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">Shipping Address</h3>
        <p style="margin:0;line-height:1.6">
          <strong>{{ $order->customer_name }}</strong><br>
          {{ $order->shipping_address_line }}<br>
          {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
          Phone: {{ $order->customer_phone }}
        </p>
      </div>

      <div>
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">Payment Summary</h3>
        <div class="summary" style="padding:.8rem;border-radius:14px">
          <div class="r"><span>Subtotal</span><span>{{ $order->formatted_subtotal }}</span></div>
          @if($order->discount > 0)
            <div class="r"><span>Discount</span><span style="color:var(--emerald)">− {{ $order->formatted_discount }}</span></div>
          @endif
          <div class="r"><span>Delivery</span><span>{{ $order->formatted_delivery_fee }}</span></div>
          <div class="r t"><span>Total Amount</span><span>{{ $order->formatted_total }}</span></div>
        </div>
        <p class="mono" style="margin:.5rem 0 0;color:var(--muted);font-size:.82rem">
          Method: {{ $order->payment_method }} · Status: {{ ucfirst($order->payment_status) }}
        </p>
      </div>
    </div>
  </div>
</main>
@endsection
