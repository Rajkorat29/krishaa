@extends('layouts.admin')

@section('page_title', 'Categories')

@section('content')
<div style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);gap:2rem;align-items:start">
  <!-- Categories List -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Categories List</h2>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Products</th>
            <th>Order</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($categories as $cat)
            <tr>
              <td><strong>{{ $cat->name }}</strong></td>
              <td class="mono">{{ $cat->slug }}</td>
              <td>{{ $cat->products_count }} pieces</td>
              <td>{{ $cat->sort_order }}</td>
              <td>
                <span class="badge {{ $cat->is_active ? 'success' : 'muted' }}">
                  {{ $cat->is_active ? 'Active' : 'Hidden' }}
                </span>
              </td>
              <td>
                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" data-confirm="Delete category '{{ $cat->name }}'?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger" {{ $cat->products_count > 0 ? 'disabled' : '' }}>Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Category Form -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Add Category</h2>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="form">
      @csrf
      <div class="fld">
        <label for="categoryName">Category Name *</label>
        <input class="in" type="text" id="categoryName" name="name" placeholder="e.g. Brooches" required>
      </div>

      <div class="fld">
        <label for="categorySlug">Slug (Optional)</label>
        <input class="in" type="text" id="categorySlug" name="slug" placeholder="e.g. brooches">
      </div>

      <div class="fld">
        <label for="cOrder">Sort Order</label>
        <input class="in" type="number" id="cOrder" name="sort_order" value="10">
      </div>

      <label class="check">
        <input type="checkbox" name="is_active" value="1" checked> Active in storefront
      </label>

      <button type="submit" class="btn solid" style="justify-self:start;margin-top:.5rem">Create Category</button>
    </form>
  </div>
</div>
@endsection
