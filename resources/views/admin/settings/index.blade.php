@extends('layouts.admin')

@section('page_title', 'Store Settings')

@section('content')
<div class="admin-card" style="max-width:900px">
  <div class="admin-card-h">
    <h2>Global Storefront Settings</h2>
    <span class="muted">Live values automatically reflect across all customer-facing views.</span>
  </div>

  <form action="{{ route('admin.settings.update') }}" method="POST" class="form">
    @csrf

    <div class="row2">
      <div class="fld">
        <label for="sName">Site Brand Name</label>
        <input class="in" type="text" id="sName" name="site_name" value="{{ $settings['site_name'] ?? 'Daisy' }}" required>
      </div>
      <div class="fld">
        <label for="sTag">Headline Tagline</label>
        <input class="in" type="text" id="sTag" name="tagline" value="{{ $settings['tagline'] ?? 'Jewellery with a loud heart.' }}">
      </div>
    </div>

    <div class="fld">
      <label for="sSub">Sub-tagline Paragraph</label>
      <input class="in" type="text" id="sSub" name="sub_tagline" value="{{ $settings['sub_tagline'] ?? "Handmade gold, silver and gemstone pieces in colours that don't whisper." }}">
    </div>

    <div class="row2">
      <div class="fld">
        <label for="sEmail">Contact Email</label>
        <input class="in" type="email" id="sEmail" name="contact_email" value="{{ $settings['contact_email'] ?? 'hello@daisyjewels.test' }}">
      </div>
      <div class="fld">
        <label for="sPhone">Contact Phone</label>
        <input class="in" type="text" id="sPhone" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 98765 43210' }}">
      </div>
    </div>

    <div class="row2">
      <div class="fld">
        <label for="sAddr">Studio Physical Address</label>
        <input class="in" type="text" id="sAddr" name="contact_address" value="{{ $settings['contact_address'] ?? '12 Zaveri Lane, Old Market, Mumbai 400002' }}">
      </div>
      <div class="fld">
        <label for="sHours">Studio Opening Hours</label>
        <input class="in" type="text" id="sHours" name="store_hours" value="{{ $settings['store_hours'] ?? 'Mon–Sat 11:00–20:00' }}">
      </div>
    </div>

    <div class="row2">
      <div class="fld">
        <label for="sDelivery">Default Delivery Fee (₹)</label>
        <input class="in" type="number" step="0.01" id="sDelivery" name="delivery_fee" value="{{ $settings['delivery_fee'] ?? '0' }}">
      </div>
      <div class="fld">
        <label for="sThreshold">Free Delivery Threshold (₹, 0 for always free)</label>
        <input class="in" type="number" step="0.01" id="sThreshold" name="free_delivery_threshold" value="{{ $settings['free_delivery_threshold'] ?? '0' }}">
      </div>
    </div>

    <div class="row2">
      <div class="fld">
        <label for="sInsta">Instagram URL</label>
        <input class="in" type="url" id="sInsta" name="instagram_url" value="{{ $settings['instagram_url'] ?? 'https://www.instagram.com/' }}">
      </div>
      <div class="fld">
        <label for="sYt">YouTube URL</label>
        <input class="in" type="url" id="sYt" name="youtube_url" value="{{ $settings['youtube_url'] ?? 'https://www.youtube.com/' }}">
      </div>
    </div>

    <div class="fld">
      <label for="sFoot">Footer Note / Hallmarking Guarantee</label>
      <textarea class="in" id="sFoot" name="footer_note" rows="2">{{ $settings['footer_note'] ?? 'Hallmarked 22K and 18K gold, 925 sterling silver, ethically sourced stones.' }}</textarea>
    </div>

    <button type="submit" class="btn solid" style="justify-self:start;margin-top:.8rem;padding:1rem 2.2rem">
      Save All Settings
    </button>
  </form>
</div>
@endsection
