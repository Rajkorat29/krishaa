<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>New Order Alert</title>
  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#F7F4FB;margin:0;padding:20px;color:#1B1530}
    .card{background:#FFFFFF;border-radius:18px;max-width:600px;margin:0 auto;padding:28px;border:1px solid #D9D1E8;box-shadow:0 4px 12px rgba(0,0,0,0.05)}
    .header{border-bottom:2px solid #1B1530;padding-bottom:16px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center}
    .badge{display:inline-block;padding:4px 10px;background:#D81B66;color:#FFF;border-radius:999px;font-size:12px;font-weight:700;text-transform:uppercase}
    .badge-status{background:#0B8A64;color:#FFF}
    .mono{font-family:ui-monospace,"SF Mono",Menlo,monospace}
    table{width:100%;border-collapse:collapse;margin:16px 0}
    th{text-align:left;padding:10px;background:#F7F4FB;font-size:12px;text-transform:uppercase;color:#645C7E}
    td{padding:12px 10px;border-bottom:1px solid #D9D1E8;font-size:14px}
    .total-row td{font-weight:700;font-size:16px;border-top:2px solid #1B1530;border-bottom:none}
    .info-box{background:#F7F4FB;border-radius:12px;padding:14px;margin-bottom:16px;font-size:14px}
    .btn{display:inline-block;background:#1B1530;color:#FFFFFF;text-decoration:none;padding:10px 20px;border-radius:999px;font-weight:600;font-size:14px;margin-top:16px}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h2 style="margin:0;font-size:22px;color:#1B1530">💎 daisy <small style="color:#D81B66;font-size:13px">STORE ALERT</small></h2>
      <span class="badge">New Order</span>
    </div>

    <p style="font-size:16px;margin:0 0 16px">
      A new order has been placed on <strong>Daisy</strong>!
    </p>

    <div class="info-box">
      <p style="margin:0 0 6px"><strong>Order Number:</strong> <span class="mono">{{ $order->order_number }}</span></p>
      <p style="margin:0 0 6px"><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
      <p style="margin:0 0 6px"><strong>Email:</strong> {{ $order->customer_email }}</p>
      <p style="margin:0 0 6px"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
      <p style="margin:0 0 6px"><strong>Payment Method:</strong> {{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})</p>
      <p style="margin:0"><strong>Placed At:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <h3 style="margin:18px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Shipping Address</h3>
    <div class="info-box">
      <p style="margin:0;line-height:1.5">
        {{ $order->shipping_address_line }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} - <strong>{{ $order->shipping_pincode }}</strong>
      </p>
    </div>

    @if($order->notes)
      <h3 style="margin:18px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Customer Note</h3>
      <div class="info-box" style="background:#FFF8E7;border:1px solid #F2B31B">
        <p style="margin:0;font-style:italic">"{{ $order->notes }}"</p>
      </div>
    @endif

    <h3 style="margin:20px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Ordered Items</h3>
    <table>
      <thead>
        <tr>
          <th>Piece</th>
          <th>Size</th>
          <th>Qty</th>
          <th>Price</th>
          <th style="text-align:right">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $it)
          <tr>
            <td><strong>{{ $it->product_name }}</strong></td>
            <td>{{ $it->size_label ?: 'Standard' }}</td>
            <td>{{ $it->quantity }}</td>
            <td>{{ $it->formatted_unit_price }}</td>
            <td style="text-align:right"><b>{{ $it->formatted_subtotal }}</b></td>
          </tr>
        @endforeach
        <tr>
          <td colspan="4" style="text-align:right;color:#645C7E">Subtotal</td>
          <td style="text-align:right">{{ $order->formatted_subtotal }}</td>
        </tr>
        @if($order->discount > 0)
          <tr>
            <td colspan="4" style="text-align:right;color:#0B8A64">Discount ({{ $order->coupon_code }})</td>
            <td style="text-align:right;color:#0B8A64">-{{ $order->formatted_discount }}</td>
          </tr>
        @endif
        @if($order->delivery_fee > 0)
          <tr>
            <td colspan="4" style="text-align:right;color:#645C7E">Delivery Fee</td>
            <td style="text-align:right">{{ $order->formatted_delivery_fee }}</td>
          </tr>
        @endif
        <tr class="total-row">
          <td colspan="4" style="text-align:right">Grand Total</td>
          <td style="text-align:right;color:#D81B66">{{ $order->formatted_total }}</td>
        </tr>
      </tbody>
    </table>

    <div style="text-align:center;margin-top:24px">
      <a href="{{ route('admin.orders.show', $order->id) }}" class="btn">View Order in Admin Panel →</a>
    </div>
  </div>
</body>
</html>
