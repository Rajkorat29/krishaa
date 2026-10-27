@extends('layouts.admin')

@section('page_title', 'Products Management')

@section('content')
<div class="admin-card">
  <div class="admin-card-h">
    <div style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap">
      <a href="{{ route('admin.products.index') }}" class="btn sm {{ request('scope') !== 'trashed' ? 'solid' : '' }}">
        All Active ({{ \App\Models\Product::count() }})
      </a>
      <a href="{{ route('admin.products.index', ['scope' => 'trashed']) }}" class="btn sm {{ request('scope') === 'trashed' ? 'solid' : '' }}">
        Trash ({{ \App\Models\Product::onlyTrashed()->count() }})
      </a>
    </div>

    <div>
      <a href="{{ route('admin.products.create') }}" class="btn solid">
        <svg aria-hidden="true" style="width:1.1rem;height:1.1rem"><use href="#plus"/></svg>Add New Piece
      </a>
    </div>
  </div>

  <!-- Filters Bar -->
  <form method="GET" action="{{ route('admin.products.index') }}" style="display:flex;gap:.8rem;flex-wrap:wrap;margin-bottom:1.2rem">
    @if(request('scope'))
      <input type="hidden" name="scope" value="{{ request('scope') }}">
    @endif

    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, SKU, stone..." class="in" style="max-width:250px">

    <select name="category_id" class="in" style="max-width:160px">
      <option value="">All Categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>

    <select name="metal_id" class="in" style="max-width:160px">
      <option value="">All Metals</option>
      @foreach($metals as $m)
        <option value="{{ $m->id }}" {{ request('metal_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
      @endforeach
    </select>

    <select name="stock_status" class="in" style="max-width:150px">
      <option value="">All Stock</option>
      <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock (&le; 5)</option>
      <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
    </select>

    <button type="submit" class="btn">Filter</button>
    <a href="{{ route('admin.products.index') }}" class="btn">Reset</a>
  </form>

  <!-- Table -->
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Piece</th>
          <th>SKU</th>
          <th>Category</th>
          <th>Metal</th>
          <th>Price / MRP</th>
          <th>Stock</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          @php
              $cover = $p->coverImage();
          @endphp
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:.8rem">
                <div class="pimg card {{ $p->color_class }}" style="width:44px;height:44px;min-width:44px;min-height:44px;max-width:44px;max-height:44px;border-radius:10px;overflow:hidden;flex:none;display:flex;align-items:center;justify-content:center;">
                  @if($cover)
                    <img src="{{ asset('storage/' . $cover) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                  @else
                    <svg class="art" style="width:100%;height:100%;display:block;padding:4px" aria-hidden="true"><use href="#{{ $p->art_symbol }}"/></svg>
                  @endif
                </div>
                <div>
                  <strong>{{ $p->name }}</strong>
                  @if($p->is_featured)
                    <span class="badge warning" style="margin-left:.3rem">Featured</span>
                  @endif
                </div>
              </div>
            </td>
            <td class="mono">{{ $p->sku }}</td>
            <td>{{ $p->category->name ?? '—' }}</td>
            <td>{{ $p->metal->name ?? '—' }}</td>
            <td>
              <b>{{ $p->formatted_price }}</b><br>
              @if($p->mrp)
                <small class="muted"><s>{{ $p->formatted_mrp }}</s></small>
              @endif
            </td>
            <td>
              @if($p->stock <= 0)
                <span class="badge danger">Out of stock</span>
              @elseif($p->stock <= 5)
                <span class="badge warning">{{ $p->stock }} (Low)</span>
              @else
                <span class="badge success">{{ $p->stock }} in stock</span>
              @endif
            </td>
            <td>
              @if($p->is_active)
                <span class="badge success">Active</span>
              @else
                <span class="badge muted">Draft</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:.4rem">
                @if($p->trashed())
                  <form action="{{ route('admin.products.restore', $p->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn sm">Restore</button>
                  </form>
                  <form action="{{ route('admin.products.forceDelete', $p->id) }}" method="POST" data-confirm="Permanently delete this piece? This cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn sm danger">Delete Forever</button>
                  </form>
                @else
                  <a href="{{ route('admin.products.edit', $p->id) }}" class="btn sm">Edit</a>
                  <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" data-confirm="Move '{{ $p->name }}' to trash?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn sm danger">Trash</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align:center;padding:2.5rem" class="muted">
              No products found matching these filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:1.2rem">
    {{ $products->withQueryString()->links() }}
  </div>
</div>
@endsection
