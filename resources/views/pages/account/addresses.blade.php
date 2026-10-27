@extends('layouts.app')

@section('title', 'My Addresses — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Account / Addresses
  </p>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <h1 style="font-size:clamp(2rem,4vw,3rem);font-weight:800;letter-spacing:-.03em">Saved Addresses</h1>

    <div style="display:flex;gap:.6rem;flex-wrap:wrap">
      <a class="btn {{ request()->routeIs('account.orders*') ? 'solid' : '' }}" href="{{ route('account.orders') }}">Orders</a>
      <a class="btn {{ request()->routeIs('account.addresses*') ? 'solid' : '' }}" href="{{ route('account.addresses') }}">Addresses</a>
      <a class="btn {{ request()->routeIs('account.profile*') ? 'solid' : '' }}" href="{{ route('account.profile') }}">Profile</a>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);gap:2rem;align-items:start">
    <!-- Existing Addresses -->
    <div>
      @if($addresses->count() === 0)
        <div class="empty">
          <h3>No saved addresses</h3>
          <p class="muted">Add a shipping address to speed up your checkout.</p>
        </div>
      @else
        <div style="display:grid;gap:1rem">
          @foreach($addresses as $addr)
            <div class="page-box" style="margin-bottom:0">
              <div style="display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                  <h3 style="font-size:1.15rem;font-weight:700">
                    {{ $addr->name }}
                    @if($addr->is_default)
                      <span class="badge info" style="margin-left:.4rem">Default</span>
                    @endif
                  </h3>
                  <p style="margin:.3rem 0;color:var(--muted)">
                    {{ $addr->address_line }}<br>
                    {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}<br>
                    Phone: {{ $addr->phone }}
                  </p>
                </div>

                <form action="{{ route('account.addresses.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Delete this address?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="linkbtn" style="color:var(--pink)">Delete</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Add New Address Form -->
    <div class="page-box">
      <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:1rem">Add a new address</h2>

      <form action="{{ route('account.addresses.store') }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label for="aName">Full name *</label>
          <input class="in" type="text" id="aName" name="name" value="{{ old('name', auth()->user()->name) }}" required>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="aPhone">Mobile number *</label>
            <input class="in" type="tel" id="aPhone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" maxlength="10" required>
          </div>
          <div class="fld">
            <label for="aPincode">Pincode *</label>
            <input class="in" type="text" id="aPincode" name="pincode" value="{{ old('pincode') }}" maxlength="6" required>
          </div>
        </div>

        <div class="fld">
          <label for="aLine">Address line *</label>
          <textarea class="in" id="aLine" name="address_line" rows="2" placeholder="House/Flat number, building, street" required>{{ old('address_line') }}</textarea>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="aCity">City *</label>
            <input class="in" type="text" id="aCity" name="city" value="{{ old('city') }}" required>
          </div>
          <div class="fld">
            <label for="aState">State *</label>
            <input class="in" type="text" id="aState" name="state" value="{{ old('state') }}" required>
          </div>
        </div>

        <label class="check">
          <input type="checkbox" name="is_default" value="1" {{ $addresses->count() === 0 ? 'checked' : '' }}> Set as default delivery address
        </label>

        <button type="submit" class="btn solid" style="justify-self:start;margin-top:.5rem">Save address</button>
      </form>
    </div>
  </div>
</main>
@endsection
