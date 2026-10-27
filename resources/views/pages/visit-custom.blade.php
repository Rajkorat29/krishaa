@extends('layouts.app')

@section('title', 'Visit Studio & Custom Orders — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Visit &amp; Custom Orders
  </p>

  <div style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1.2fr);gap:2rem;align-items:start">
    <div>
      <div class="page-box">
        <h1 class="page-title" style="font-size:2.2rem">Visit the Studio</h1>
        <p class="content-text">
          {{ \App\Models\Setting::get('split_card_1_text', 'Meet the makers, feel the weight of a piece and get it sized on the spot. Every gold piece carries a BIS hallmark.') }}
        </p>

        <dl class="specs" style="margin-top:1.2rem">
          <dt>Address</dt>
          <dd>{{ \App\Models\Setting::get('contact_address', '12 Zaveri Lane, Old Market, Mumbai 400002') }}</dd>

          <dt>Timings</dt>
          <dd>{{ \App\Models\Setting::get('store_hours', 'Mon–Sat 11:00–20:00') }}</dd>

          <dt>Services</dt>
          <dd>Same-day laser sizing, ultrasonic cleaning, custom design appointments</dd>
        </dl>
      </div>

      <div class="page-box">
        <h2 style="font-size:1.6rem;font-weight:700;margin-bottom:.8rem">Custom Orders &amp; Heirloom Rebirth</h2>
        <p class="content-text">
          {{ \App\Models\Setting::get('split_card_2_text', 'Bring a sketch, a stone or a family heirloom. Our goldsmiths turn it into something new, with a quote before work begins.') }}
        </p>
      </div>
    </div>

    <!-- Custom Order Enquiry Form -->
    <div class="page-box">
      <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:1rem">Book a Studio Appointment or Custom Quote</h2>

      <form action="{{ route('contact.store') }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label for="cuName">Your Name *</label>
          <input class="in" type="text" id="cuName" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="cuEmail">Email Address *</label>
            <input class="in" type="email" id="cuEmail" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
          </div>
          <div class="fld">
            <label for="cuPhone">Mobile Number *</label>
            <input class="in" type="tel" id="cuPhone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required>
          </div>
        </div>

        <div class="fld">
          <label for="cuMessage">Tell us about your custom design idea or visit date *</label>
          <textarea class="in" id="cuMessage" name="message" rows="5" placeholder="Mention metal preference (22K, 18K, 925 Silver), stone or heirloom details, and your preferred appointment date/time." required>{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn solid" style="justify-self:start;padding:1rem 1.8rem">
          Submit custom enquiry <svg aria-hidden="true"><use href="#arrow"/></svg>
        </button>
      </form>
    </div>
  </div>
</main>
@endsection
