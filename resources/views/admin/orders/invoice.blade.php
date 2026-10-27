<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Tax Invoice — {{ $order->order_number }}</title>
  <style>
    body{font-family:sans-serif;color:#1B1530;margin:0;padding:2rem;font-size:14px;background:#fff}
    .invoice-wrap{max-width:800px;margin:auto;border:1px solid #D9D1E8;padding:2.5rem;border-radius:12px}
    .header{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #1B1530;padding-bottom:1.5rem;margin-bottom:1.5rem}
    .logo{font-size:2rem;font-weight:800;letter-spacing:-.04em;color:#D81B66}
    .table{width:100%;border-collapse:collapse;margin:1.5rem 0}
    .table th{background:#F7F4FB;border-bottom:2px solid #1B1530;padding:.8rem;text-align:left;font-size:12px;text-transform:uppercase}
    .table td{border-bottom:1px solid #D9D1E8;padding:.8rem}
    .total-box{float:right;width:280px;margin-top:1rem}
    .total-row{display:flex;justify-content:space-between;padding:.3rem 0}
    .grand-total{border-top:2px solid #1B1530;padding-top:.5rem;font-weight:800;font-size:1.15rem}
    .print-bar{margin-bottom:1rem;text-align:right}
    .btn{padding:.5rem 1rem;background:#1B1530;color:#fff;border:0;border-radius:999px;cursor:pointer;font-weight:600}
    @media print{
      .print-bar{display:none}
      .invoice-wrap{border:0;padding:0}
    }
  </style>
</head>
<body>
  <div class="invoice-wrap">
    <div class="print-bar">
      <button class="btn" onclick="window.print()">Print Invoice</button>
    </div>

    <div class="header">
      <div>
        <div class="logo">{{ \App\Models\Setting::get('site_name', 'daisy') }}</div>
        <p style="margin:.3rem 0;color:#645C7E">
          {{ \App\Models\Setting::get('contact_address', '12 Zaveri Lane, Old Market, Mumbai 400002') }}<br>
          GSTIN: 27AAAAA0000A1Z5 · BIS Reg: HM-749204
        </p>
      </div>
      <div style="text-align:right">
        <h2 style="margin:0;font-size:1.4rem">TAX INVOICE</h2>
        <p style="margin:.3rem 0;font-family:monospace"><b>{{ $order->order_number }}</b></p>
        <p style="margin:.3rem 0;color:#645C7E">Date: {{ $order->created_at->format('d M Y') }}</p>
      </div>
    </div>

    <div style="display:flex;justify-content:space-between;gap:2rem;margin-bottom:1.5rem">
      <div>
        <strong style="text-transform:uppercase;font-size:11px;color:#645C7E">Billed &amp; Shipped To:</strong>
        <p style="margin:.4rem 0;line-height:1.5">
          <strong>{{ $order->customer_name }}</strong><br>
          {{ $order->shipping_address_line }}<br>
          {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
          Phone: {{ $order->customer_phone }}
        </p>
      </div>
      <div style="text-align:right">
        <strong style="text-transform:uppercase;font-size:11px;color:#645C7E">Payment Details:</strong>
        <p style="margin:.4rem 0;line-height:1.5">
          Method: <strong>{{ $order->payment_method }}</strong><br>
          Status: <strong>{{ ucfirst($order->payment_status) }}</strong>
        </p>
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Sl.</th>
          <th>Piece Description</th>
          <th>Size</th>
          <th>Unit Price</th>
          <th>Qty</th>
          <th style="text-align:right">Total Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $idx => $it)
          <tr>
            <td>{{ $idx + 1 }}</td>
            <td><strong>{{ $it->product_name }}</strong></td>
            <td>Size {{ $it->size_label }}</td>
            <td>{{ $it->formatted_unit_price }}</td>
            <td>{{ $it->quantity }}</td>
            <td style="text-align:right">{{ $it->formatted_subtotal }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="overflow:hidden">
      <div class="total-box">
        <div class="total-row"><span>Subtotal</span><span>{{ $order->formatted_subtotal }}</span></div>
        @if($order->discount > 0)
          <div class="total-row"><span>Discount</span><span>− {{ $order->formatted_discount }}</span></div>
        @endif
        <div class="total-row"><span>Delivery Charges</span><span>{{ $order->formatted_delivery_fee }}</span></div>
        <div class="total-row grand-total"><span>Grand Total (INR)</span><span>{{ $order->formatted_total }}</span></div>
      </div>
    </div>

    <div style="margin-top:3rem;border-top:1px solid #D9D1E8;padding-top:1rem;color:#645C7E;font-size:12px">
      <p style="margin:0">Thank you for choosing {{ \App\Models\Setting::get('site_name', 'Daisy') }}. All gold pieces are certified hallmarked. For assistance, contact {{ \App\Models\Setting::get('contact_email', 'hello@daisyjewels.test') }}.</p>
    </div>
  </div>
</body>
</html>
