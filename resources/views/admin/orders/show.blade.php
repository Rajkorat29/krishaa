@extends('layouts.admin')

@section('page_title', 'Order ' . $order->order_number)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
  <div>
    <h2 style="font-size:1.6rem;font-weight:800;letter-spacing:-.03em">
      Order <span class="mono">{{ $order->order_number }}</span>
    </h2>
    <span class="mono" style="color:var(--muted)">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</span>
  </div>

  <div style="display:flex;gap:.8rem">
    <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn sm solid">
      Print Invoice ↗
    </a>
    <a href="{{ route('admin.orders.index') }}" class="btn sm">← Back to Orders</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:1.5rem">
  <!-- Left: Ordered Items & Shipping Details -->
  <div style="display:grid;gap:1.5rem">
    <!-- Ordered Items Card -->
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-h">
        <h2>Items in Order</h2>
        <span class="badge info">{{ $order->items->sum('quantity') }} items</span>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Item</th>
              <th>Size</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $it)
              <tr>
                <td><strong>{{ $it->product_name }}</strong></td>
                <td class="mono">Size {{ $it->size_label }}</td>
                <td>{{ $it->formatted_unit_price }}</td>
                <td>{{ $it->quantity }}</td>
                <td><b>{{ $it->formatted_subtotal }}</b></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="margin-top:1.2rem;display:flex;justify-content:flex-end">
        <div class="summary" style="width:300px">
          <div class="r"><span>Subtotal</span><span>{{ $order->formatted_subtotal }}</span></div>
          @if($order->discount > 0)
            <div class="r"><span>Discount</span><span style="color:var(--emerald)">− {{ $order->formatted_discount }}</span></div>
          @endif
          <div class="r"><span>Delivery Fee</span><span>{{ $order->formatted_delivery_fee }}</span></div>
          <div class="r t"><span>Grand Total</span><span>{{ $order->formatted_total }}</span></div>
        </div>
      </div>
    </div>

    <!-- Customer & Shipping Card -->
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-h">
        <h2>Customer &amp; Shipping Address</h2>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
        <div>
          <span class="lbl">Customer Details</span>
          <p style="margin:.3rem 0;line-height:1.6">
            <strong>{{ $order->customer_name }}</strong><br>
            Phone: <a href="tel:{{ $order->customer_phone }}" class="mono">{{ $order->customer_phone }}</a><br>
            Email: <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>
          </p>
        </div>

        <div>
          <span class="lbl">Delivery Destination</span>
          <p style="margin:.3rem 0;line-height:1.6">
            {{ $order->shipping_address_line }}<br>
            {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Status Control & Internal Notes -->
  <div style="display:grid;gap:1.5rem">
    <!-- Status Control Card -->
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-h">
        <h2>Update Status</h2>
      </div>

      <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="form">
        @csrf
        @method('PATCH')

        <div class="fld">
          <label for="orderStatus">Fulfillment Status</label>
          <select name="order_status" id="orderStatus" class="in">
            @foreach($statuses as $st)
              <option value="{{ $st }}" {{ $order->order_status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
        </div>

        <div class="fld">
          <label for="paymentStatus">Payment Status</label>
          <select name="payment_status" id="paymentStatus" class="in">
            @foreach($payments as $pm)
              <option value="{{ $pm }}" {{ $order->payment_status === $pm ? 'selected' : '' }}>{{ ucfirst($pm) }}</option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn solid" style="justify-self:start;margin-top:.5rem">Save status</button>
      </form>
    </div>

    <!-- Internal Notes Card -->
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-h">
        <h2>Internal Staff Notes</h2>
      </div>

      @if($order->notes)
        <div style="background:var(--bg);border:1px solid var(--line);border-radius:14px;padding:1rem;margin-bottom:1rem;white-space:pre-wrap;font-size:.92rem">
          {{ $order->notes }}
        </div>
      @endif

      <form action="{{ route('admin.orders.note', $order->id) }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label for="noteInput">Add note</label>
          <textarea class="in" id="noteInput" name="note" rows="3" placeholder="e.g. Verified pincode with courier partner..." required></textarea>
        </div>
        <button type="submit" class="btn" style="justify-self:start">Add note</button>
      </form>
    </div>
  </div>
</div>
@endsection
