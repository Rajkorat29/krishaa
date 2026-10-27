@extends('layouts.admin')

@section('page_title', 'Customers')

@section('content')
<div class="admin-card">
  <div class="admin-card-h">
    <h2>Registered Customers</h2>
    <form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex;gap:.5rem">
      <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..." class="in" style="max-width:250px">
      <button type="submit" class="btn sm">Search</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Customer</th>
          <th>Contact</th>
          <th>Joined</th>
          <th>Orders Placed</th>
          <th>Account Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $c)
          <tr>
            <td>
              <strong>{{ $c->name }}</strong>
            </td>
            <td>
              <span>{{ $c->email }}</span><br>
              <small class="mono" style="color:var(--muted)">{{ $c->phone ?: 'No phone' }}</small>
            </td>
            <td class="mono" style="color:var(--muted)">{{ $c->created_at->format('d M Y') }}</td>
            <td>
              <a href="{{ route('admin.orders.index', ['q' => $c->email]) }}" class="badge info">
                {{ $c->orders_count }} {{ $c->orders_count === 1 ? 'order' : 'orders' }}
              </a>
            </td>
            <td>
              @if($c->is_blocked)
                <span class="badge danger">Blocked</span>
              @else
                <span class="badge success">Active</span>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.customers.block', $c->id) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($c->is_blocked)
                  <button type="submit" class="btn sm">Unblock</button>
                @else
                  <button type="submit" class="btn sm danger" data-confirm="Block customer {{ $c->name }} from placing orders?">Block</button>
                @endif
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center;padding:2.5rem" class="muted">No customers found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:1.2rem">
    {{ $customers->withQueryString()->links() }}
  </div>
</div>
@endsection
