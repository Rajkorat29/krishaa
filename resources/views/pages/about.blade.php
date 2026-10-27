@extends('layouts.app')

@section('title', 'About Us — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem;max-width:900px">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / About us
  </p>

  <div class="page-box">
    <h1 class="page-title">Jewellery with a loud heart.</h1>

    <div class="content-text">
      <p style="font-size:1.25rem;font-weight:600;color:var(--pink)">
        {{ \App\Models\Setting::get('sub_tagline', "Handmade gold, silver and gemstone pieces in colours that don't whisper.") }}
      </p>

      <p>
        Founded in Mumbai, <strong>Daisy</strong> reimagines heritage Indian goldsmithing for the contemporary spirit. We believe fine jewellery is meant to be worn boldly, stacked fearlessly, and lived in daily — not locked away in bank vaults.
      </p>

      <p>
        Every single piece in our collections is handcrafted by master karigars in Jaipur, Mumbai, and Surat using ethical materials: hallmarked 22K and 18K gold, BIS certified 925 sterling silver, certified lab diamonds, and natural, unheated colored gemstones.
      </p>

      <h3 style="font-size:1.4rem;font-weight:700;margin:2rem 0 .8rem">Our Craft Standards</h3>
      <ul style="display:grid;gap:.8rem;padding-left:1.2rem">
        <li><strong>Hallmarked Purity:</strong> Every gold piece carries a BIS hallmark stamp, guaranteeing certified purity.</li>
        <li><strong>Hand-Enamelled Vitreous Art:</strong> Traditional Jaipuri Meenakari enamel, fired at 800°C for lifetime color brilliance.</li>
        <li><strong>Transparent Pricing:</strong> Fair pricing with gold weights and making charges explicitly stated upfront.</li>
        <li><strong>Zero Compromise:</strong> 7-day easy returns and lifetime cleaning service at our studio.</li>
      </ul>

      <div style="margin-top:2.5rem;display:flex;gap:1rem;flex-wrap:wrap">
        <a class="btn solid" href="{{ route('rings') }}">Explore the rings collection</a>
        <a class="btn" href="{{ route('pages.visit-custom') }}">Visit our studio &amp; custom orders</a>
      </div>
    </div>
  </div>
</main>
@endsection
