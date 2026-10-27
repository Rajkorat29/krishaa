@extends('layouts.app')

@section('title', 'My Orders — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Account / Orders
  </p>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <h1 style="font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-.03em">My Orders</h1>

    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      <a class="btn {{ request()->routeIs('account.orders*') ? 'solid' : '' }}" href="{{ route('account.orders') }}">Orders</a>
      <a class="btn {{ request()->routeIs('account.addresses*') ? 'solid' : '' }}" href="{{ route('account.addresses') }}">Addresses</a>
      <a class="btn {{ request()->routeIs('account.profile*') ? 'solid' : '' }}" href="{{ route('account.profile') }}">Profile</a>
    </div>
  </div>

  @if($orders->count() === 0)
    <div class="empty">
      <h3>No orders yet</h3>
      <p class="muted">You haven't placed any orders yet. Discover our latest jewellery collections.</p>
      <a class="btn solid" href="{{ route('rings') }}" style="margin-top:1rem">Explore rings</a>
    </div>
  @else
    <div style="display:grid;gap:1.5rem">
      @foreach($orders as $order)
        <div class="page-box" style="margin-bottom:0">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;border-bottom:1px solid var(--line);padding-bottom:1rem">
            <div>
              <span class="mono" style="color:var(--muted);font-size:.82rem">Order Number</span>
              <h3 style="font-size:1.25rem;font-weight:800">{{ $order->order_number }}</h3>
              <p class="mono" style="margin:.2rem 0 0;color:var(--muted)">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
              <div>
                <span class="mono" style="color:var(--muted);font-size:.82rem">Total Amount</span>
                <p style="margin:.2rem 0 0;font-size:1.2rem;font-weight:800;color:var(--pink)">{{ $order->formatted_total }}</p>
              </div>

              @php
                  $badgeClasses = [
                      'placed' => 'warning',
                      'confirmed' => 'info',
                      'packed' => 'info',
                      'shipped' => 'info',
                      'delivered' => 'success',
                      'cancelled' => 'danger',
                  ];
              @endphp
              <span class="badge {{ $badgeClasses[$order->order_status] ?? 'muted' }}" style="padding:.4rem .8rem;font-size:.85rem">
                {{ ucfirst($order->order_status) }}
              </span>
            </div>
          </div>

          <!-- Items Preview -->
          <div style="display:grid;gap:.8rem;padding:1.2rem 0">
            @foreach($order->items as $item)
              <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
                <div>
                  <strong>{{ $item->product_name }}</strong>
                  <span class="mono" style="color:var(--muted);margin-left:.5rem">Size {{ $item->size_label }} · Qty {{ $item->quantity }}</span>
                </div>
                <b>{{ $item->formatted_subtotal }}</b>
              </div>
            @endforeach
          </div>

          <!-- Actions -->
          <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--line);padding-top:1rem;flex-wrap:wrap;gap:1rem">
            <span class="muted" style="font-size:.9rem">Payment: {{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})</span>

            <div style="display:flex;gap:.8rem">
              <a class="btn" href="{{ route('account.orders.show', $order->order_number) }}">
                View order timeline &amp; details
              </a>

              @if($order->canCancel())
                <form action="{{ route('account.orders.cancel', $order->order_number) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel order {{ $order->order_number }}?');">
                  @csrf
                  <button type="submit" class="btn danger">Cancel order</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</main>
@endsection
