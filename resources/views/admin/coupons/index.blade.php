@extends('layouts.admin')

@section('page_title', 'Coupons')

@section('content')
<div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:2rem;align-items:start">
  <!-- Coupons List -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Discount Coupons</h2>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Code</th>
            <th>Discount</th>
            <th>Min Order</th>
            <th>Uses</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($coupons as $cp)
            <tr>
              <td><b class="mono">{{ $cp->code }}</b></td>
              <td>
                @if($cp->type === 'percent')
                  <span class="badge info">{{ (int)$cp->value }}% OFF</span>
                @else
                  <span class="badge success">₹{{ (int)$cp->value }} OFF</span>
                @endif
              </td>
              <td>{{ $cp->min_order > 0 ? \App\Models\Product::formatInr($cp->min_order) : 'None' }}</td>
              <td class="mono">{{ $cp->used_count }} / {{ $cp->max_uses ?: '∞' }}</td>
              <td>
                <span class="badge {{ $cp->is_active ? 'success' : 'muted' }}">
                  {{ $cp->is_active ? 'Active' : 'Disabled' }}
                </span>
              </td>
              <td>
                <form action="{{ route('admin.coupons.destroy', $cp->id) }}" method="POST" data-confirm="Delete coupon '{{ $cp->code }}'?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Coupon Form -->
  <div class="admin-card">
    <div class="admin-card-h">
      <h2>Create New Coupon</h2>
    </div>

    <form action="{{ route('admin.coupons.store') }}" method="POST" class="form">
      @csrf
      <div class="fld">
        <label for="cCode">Coupon Code *</label>
        <input class="in" type="text" id="cCode" name="code" placeholder="e.g. FESTIVE20" style="text-transform:uppercase" required>
      </div>

      <div class="row2">
        <div class="fld">
          <label for="cType">Discount Type *</label>
          <select name="type" id="cType" class="in" required>
            <option value="percent">Percentage (%)</option>
            <option value="flat">Flat Amount (₹)</option>
          </select>
        </div>
        <div class="fld">
          <label for="cVal">Value *</label>
          <input class="in" type="number" step="0.01" id="cVal" name="value" placeholder="10 or 500" required>
        </div>
      </div>

      <div class="row2">
        <div class="fld">
          <label for="cMin">Minimum Order (₹)</label>
          <input class="in" type="number" step="0.01" id="cMin" name="min_order" value="0">
        </div>
        <div class="fld">
          <label for="cMaxUses">Maximum Uses</label>
          <input class="in" type="number" id="cMaxUses" name="max_uses" placeholder="Leave empty for unlimited">
        </div>
      </div>

      <label class="check">
        <input type="checkbox" name="is_active" value="1" checked> Active immediately
      </label>

      <button type="submit" class="btn solid" style="justify-self:start;margin-top:.5rem">Create Coupon</button>
    </form>
  </div>
</div>
@endsection
