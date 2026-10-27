@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<!-- Stat Cards -->
<div class="stat-grid">
  <div class="stat-card">
    <span class="lbl">Total Sales</span>
    <span class="val" style="color:var(--pink)">{{ \App\Models\Product::formatInr($totalSales) }}</span>
  </div>

  <div class="stat-card">
    <span class="lbl">Orders Today</span>
    <span class="val">{{ $ordersToday }}</span>
  </div>

  <div class="stat-card">
    <span class="lbl">Pending Orders</span>
    <span class="val" style="color:#c98200">{{ $pendingOrders }}</span>
  </div>

  <div class="stat-card">
    <span class="lbl">Total Customers</span>
    <span class="val">{{ $customersCount }}</span>
  </div>
</div>

<!-- 30-Day Sales Chart -->
<div class="admin-card">
  <div class="admin-card-h">
    <h2>30-Day Sales Performance</h2>
    <span class="mono" style="color:var(--muted)">Daily Revenue (INR)</span>
  </div>
  <div style="height:280px;position:relative">
    <canvas id="salesChart" data-labels="{{ json_encode($chartLabels) }}" data-values="{{ json_encode($chartValues) }}"></canvas>
  </div>
</div>

<!-- Two Columns: Latest Orders & Top Products -->
<div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:1.5rem">
  <!-- Latest Orders -->
  <div class="admin-card" style="margin-bottom:0">
    <div class="admin-card-h">
      <h2>Recent Orders</h2>
      <a href="{{ route('admin.orders.index') }}" class="btn sm">View all →</a>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestOrders as $ord)
            <tr>
              <td>
                <b class="mono">{{ $ord->order_number }}</b><br>
                <small class="mono" style="color:var(--muted)">{{ $ord->created_at->format('d M, h:i A') }}</small>
              </td>
              <td>{{ $ord->customer_name }}</td>
              <td><b>{{ $ord->formatted_total }}</b></td>
              <td>
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
                <span class="badge {{ $badgeCls[$ord->order_status] ?? 'muted' }}">{{ ucfirst($ord->order_status) }}</span>
              </td>
              <td>
                <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn sm">View</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center;padding:2rem" class="muted">No orders placed yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Top Products & Low Stock Alerts -->
  <div style="display:grid;gap:1.5rem">
    <!-- Top Products -->
    <div class="admin-card" style="margin-bottom:0">
      <div class="admin-card-h">
        <h2>Top Pieces</h2>
        <a href="{{ route('admin.products.index') }}" class="btn sm">All pieces →</a>
      </div>

      <div style="display:grid;gap:.8rem">
        @foreach($topProducts as $p)
          @php $cover = $p->coverImage(); @endphp
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.8rem">
            <div style="display:flex;align-items:center;gap:.75rem">
              <div class="pimg card {{ $p->color_class }}" style="width:38px;height:38px;min-width:38px;min-height:38px;border-radius:8px;overflow:hidden;flex:none;display:flex;align-items:center;justify-content:center;">
                @if($cover)
                  <img src="{{ asset('storage/' . $cover) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                @else
                  <svg class="art" style="width:100%;height:100%;display:block;padding:3px" aria-hidden="true"><use href="#{{ $p->art_symbol }}"/></svg>
                @endif
              </div>
              <div>
                <strong>{{ $p->name }}</strong><br>
                <small class="mono" style="color:var(--muted)">{{ $p->category->name ?? '' }} · {{ $p->metal->name ?? '' }}</small>
              </div>
            </div>
            <div style="text-align:right">
              <b>{{ $p->formatted_price }}</b><br>
              <small class="mono" style="color:var(--emerald)">★ {{ number_format($p->rating_avg, 1) }} ({{ $p->rating_count }})</small>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
      <div class="admin-card" style="margin-bottom:0;border-color:var(--pink)">
        <div class="admin-card-h">
          <h2 style="color:var(--pink)">⚠️ Low Stock Alert</h2>
        </div>
        <div style="display:grid;gap:.6rem">
          @foreach($lowStockProducts as $lp)
            <div style="display:flex;justify-content:space-between;align-items:center">
              <span>{{ $lp->name }}</span>
              <span class="badge danger">{{ $lp->stock }} left</span>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
