@extends('layouts.app')

@section('title', 'Contact Us — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Contact
  </p>

  <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr);gap:2rem;align-items:start">
    <div class="page-box">
      <h1 class="page-title" style="font-size:2.2rem">We'd love to hear from you.</h1>
      <p style="color:var(--muted);font-size:1.05rem;line-height:1.6;margin-bottom:1.5rem">
        Have questions about ring sizes, custom gemstone commissions, or an order in transit? Write to us or call our studio team.
      </p>

      <div style="display:grid;gap:1.2rem">
        <div>
          <span class="lbl">Email</span>
          <p style="margin:.2rem 0 0;font-weight:600;font-size:1.1rem">
            <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'hello@daisyjewels.test') }}" style="color:var(--pink)">
              {{ \App\Models\Setting::get('contact_email', 'hello@daisyjewels.test') }}
            </a>
          </p>
        </div>

        <div>
          <span class="lbl">Phone / WhatsApp</span>
          <p style="margin:.2rem 0 0;font-weight:600;font-size:1.1rem">
            {{ \App\Models\Setting::get('contact_phone', '+91 98765 43210') }}
          </p>
        </div>

        <div>
          <span class="lbl">Studio Hours</span>
          <p style="margin:.2rem 0 0;font-weight:600">
            {{ \App\Models\Setting::get('store_hours', 'Monday–Saturday 11:00 AM – 8:00 PM IST') }}
          </p>
        </div>

        <div>
          <span class="lbl">Store Location</span>
          <p style="margin:.2rem 0 0;line-height:1.6">
            {{ \App\Models\Setting::get('contact_address', '12 Zaveri Lane, Old Market, Mumbai 400002') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Enquiry Form -->
    <div class="page-box">
      <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:1.2rem">Send us a message</h2>

      <form action="{{ route('contact.store') }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label for="mName">Your Name *</label>
          <input class="in" type="text" id="mName" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="mEmail">Email Address *</label>
            <input class="in" type="email" id="mEmail" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
          </div>
          <div class="fld">
            <label for="mPhone">Phone Number (Optional)</label>
            <input class="in" type="tel" id="mPhone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}">
          </div>
        </div>

        <div class="fld">
          <label for="mMsg">Your Message *</label>
          <textarea class="in" id="mMsg" name="message" rows="5" placeholder="How can our jewelers help you today?" required>{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="btn solid" style="justify-self:start;padding:1rem 1.8rem">
          Send message <svg aria-hidden="true"><use href="#arrow"/></svg>
        </button>
      </form>
    </div>
  </div>
</main>
@endsection
