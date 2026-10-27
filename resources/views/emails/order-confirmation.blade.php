<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Order Confirmation</title>
  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#F7F4FB;margin:0;padding:20px;color:#1B1530}
    .card{background:#FFFFFF;border-radius:18px;max-width:600px;margin:0 auto;padding:28px;border:1px solid #D9D1E8}
    .header{border-bottom:2px solid #1B1530;padding-bottom:16px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center}
    .mono{font-family:ui-monospace,"SF Mono",Menlo,monospace}
    table{width:100%;border-collapse:collapse;margin:16px 0}
    th{text-align:left;padding:10px;background:#F7F4FB;font-size:12px;text-transform:uppercase;color:#645C7E}
    td{padding:12px 10px;border-bottom:1px solid #D9D1E8;font-size:14px}
    .total-row td{font-weight:700;font-size:16px;border-top:2px solid #1B1530;border-bottom:none}
    .info-box{background:#F7F4FB;border-radius:12px;padding:14px;margin-bottom:16px;font-size:14px}
    .btn{display:inline-block;background:#D81B66;color:#FFFFFF;text-decoration:none;padding:12px 24px;border-radius:999px;font-weight:600;font-size:14px;margin-top:16px}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h2 style="margin:0;font-size:24px;color:#1B1530">💎 daisy</h2>
      <span style="font-weight:700;color:#0B8A64">Order Confirmed</span>
    </div>

    <p style="font-size:16px;margin:0 0 16px">
      Dear {{ $order->customer_name }},
    </p>
    <p style="font-size:15px;line-height:1.5;margin:0 0 16px;color:#645C7E">
      Thank you for shopping with <strong>Daisy</strong>. We have received your order <strong class="mono">{{ $order->order_number }}</strong> and our artisans are preparing it for delivery.
    </p>

    <div class="info-box">
      <p style="margin:0 0 6px"><strong>Order Number:</strong> <span class="mono">{{ $order->order_number }}</span></p>
      <p style="margin:0 0 6px"><strong>Estimated Delivery:</strong> {{ now()->addDays(4)->format('l, d M Y') }}</p>
      <p style="margin:0"><strong>Payment:</strong> {{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})</p>
    </div>

    <h3 style="margin:18px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Delivery Address</h3>
    <div class="info-box">
      <p style="margin:0;line-height:1.5">
        {{ $order->customer_name }}<br>
        {{ $order->shipping_address_line }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} - <strong>{{ $order->shipping_pincode }}</strong><br>
        Phone: {{ $order->customer_phone }}
      </p>
    </div>

    <h3 style="margin:20px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Order Summary</h3>
    <table>
      <thead>
        <tr>
          <th>Piece</th>
          <th>Size</th>
          <th>Qty</th>
          <th style="text-align:right">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $it)
          <tr>
            <td><strong>{{ $it->product_name }}</strong></td>
            <td>{{ $it->size_label ?: 'Standard' }}</td>
            <td>{{ $it->quantity }}</td>
            <td style="text-align:right"><b>{{ $it->formatted_subtotal }}</b></td>
          </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="3" style="text-align:right">Total Paid / Due</td>
          <td style="text-align:right;color:#D81B66">{{ $order->formatted_total }}</td>
        </tr>
      </tbody>
    </table>

    <div style="text-align:center;margin-top:24px">
      <a href="{{ route('account.orders') }}" class="btn">Track Order in My Account →</a>
    </div>

    <p style="font-size:12px;color:#A8A0C4;text-align:center;margin-top:24px">
      Questions? Reach us at daisyjewelers940@gmail.com
    </p>
  </div>
</body>
</html>
