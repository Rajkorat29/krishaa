@extends('layouts.app')

@section('title', 'Order Confirmed ' . $order->order_number . ' — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:2rem;padding-bottom:4rem;max-width:760px">
  <div class="page-box done" style="padding:clamp(2rem,5vw,3.5rem)">
    <div class="tick">
      <svg aria-hidden="true"><use href="#check"/></svg>
    </div>

    <h1 style="font-size:clamp(2rem,4vw,2.8rem);font-weight:800;letter-spacing:-.03em">
      Thank you, {{ explode(' ', $order->customer_name)[0] }}!
    </h1>

    <p style="font-size:1.1rem;color:var(--muted);margin:0">
      Your order <b class="mono" style="color:var(--fg);font-size:1.05rem">{{ $order->order_number }}</b> has been placed successfully.
    </p>

    <!-- Details Box -->
    <div style="width:100%;text-align:left;background:var(--bg);border:1px solid var(--line);border-radius:18px;padding:1.4rem;margin:1rem 0;display:grid;gap:1rem">
      <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem;border-bottom:1px solid var(--line);padding-bottom:.8rem">
        <div>
          <span class="lbl" style="margin:0">Payment</span>
          <p style="margin:.2rem 0 0;font-weight:600">{{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})</p>
        </div>
        <div>
          <span class="lbl" style="margin:0">Estimated Delivery</span>
          <p style="margin:.2rem 0 0;font-weight:600">{{ now()->addDays(4)->format('D, d M Y') }}</p>
        </div>
        <div>
          <span class="lbl" style="margin:0">Total Amount</span>
          <p style="margin:.2rem 0 0;font-weight:700;color:var(--pink)">{{ $order->formatted_total }}</p>
        </div>
      </div>

      <div>
        <span class="lbl">Delivery Address</span>
        <p style="margin:.2rem 0 0;font-weight:500">
          {{ $order->customer_name }}<br>
          {{ $order->shipping_address_line }}<br>
          {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
          Phone: {{ $order->customer_phone }}
        </p>
      </div>

      <div style="border-top:1px solid var(--line);padding-top:.8rem">
        <span class="lbl">Ordered Items ({{ $order->items->sum('quantity') }})</span>
        <div style="display:grid;gap:.6rem;margin-top:.5rem">
          @foreach($order->items as $it)
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:.95rem">
              <span>{{ $it->product_name }} (Size {{ $it->size_label }}) × {{ $it->quantity }}</span>
              <b>{{ $it->formatted_subtotal }}</b>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:.5rem">
      <a class="btn solid" href="{{ route('account.orders') }}">View your orders</a>
      <a class="btn" href="{{ route('rings') }}">Continue shopping</a>
    </div>
  </div>
</main>
@endsection
