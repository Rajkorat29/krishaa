@extends('layouts.admin')

@section('page_title', 'Orders')

@section('content')
<div class="admin-card">
  <div class="admin-card-h">
    <h2>Customer Orders</h2>
    <a href="{{ route('admin.orders.export') }}" class="btn sm">
      Export CSV ↓
    </a>
  </div>

  <!-- Filter Bar -->
  <form method="GET" action="{{ route('admin.orders.index') }}" style="display:flex;gap:.8rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Order #, name, phone..." class="in" style="max-width:230px">

    <select name="status" class="in" style="max-width:150px">
      <option value="">All Statuses</option>
      @foreach($statuses as $st)
        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
      @endforeach
    </select>

    <select name="payment" class="in" style="max-width:150px">
      <option value="">All Payments</option>
      @foreach($payments as $pm)
        <option value="{{ $pm }}" {{ request('payment') === $pm ? 'selected' : '' }}>{{ ucfirst($pm) }}</option>
      @endforeach
    </select>

    <input type="date" name="from_date" value="{{ request('from_date') }}" class="in" style="max-width:150px">
    <input type="date" name="to_date" value="{{ request('to_date') }}" class="in" style="max-width:150px">

    <button type="submit" class="btn">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn">Reset</a>
  </form>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order</th>
          <th>Date</th>
          <th>Customer</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          @php
              $badgeCls = [
                  'placed' => 'warning',
                  'confirmed' => 'info',
                  'packed' => 'info',
                  'shipped' => 'info',
                  'delivered' => 'success',
                  'cancelled' => 'danger',
              ];
          @endphp
          <tr>
            <td><b class="mono">{{ $order->order_number }}</b></td>
            <td class="mono" style="color:var(--muted)">{{ $order->created_at->format('d M Y, h:i A') }}</td>
            <td>
              <strong>{{ $order->customer_name }}</strong><br>
              <small class="mono" style="color:var(--muted)">{{ $order->customer_phone }}</small>
            </td>
            <td>{{ $order->items->sum('quantity') }} items</td>
            <td><b>{{ $order->formatted_total }}</b></td>
            <td>
              <span class="badge {{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                {{ $order->payment_method }} ({{ ucfirst($order->payment_status) }})
              </span>
            </td>
            <td>
              <span class="badge {{ $badgeCls[$order->order_status] ?? 'muted' }}">
                {{ ucfirst($order->order_status) }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.orders.show', $order->id) }}" class="btn sm">Manage</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align:center;padding:2.5rem" class="muted">No orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:1.2rem">
    {{ $orders->withQueryString()->links() }}
  </div>
</div>
@endsection
