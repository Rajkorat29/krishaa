@extends('layouts.app')

@section('title', 'Terms & Conditions — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem;max-width:850px">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Terms &amp; Conditions
  </p>

  <div class="page-box">
    <h1 class="page-title">Terms &amp; Conditions</h1>
    <div class="content-text">
      <p>Last updated: {{ date('F Y') }}</p>
      <p>Welcome to <strong>Daisy</strong>. By accessing our website, placing an order, or commissioning a bespoke jewellery piece, you agree to comply with and be bound by the following terms.</p>
      <h3 style="font-size:1.3rem;font-weight:700;margin:1.5rem 0 .5rem">1. Authenticity &amp; Hallmarking</h3>
      <p>All gold jewellery sold on Daisy is certified by the Bureau of Indian Standards (BIS) and stamped with the official hallmark. Sterling silver pieces bear the standard 925 authenticity stamp.</p>
      <h3 style="font-size:1.3rem;font-weight:700;margin:1.5rem 0 .5rem">2. Delivery &amp; Transit Insurance</h3>
      <p>Every shipment is fully insured in transit until delivery is signed for by the recipient. Estimated delivery times are 4-6 business days across India.</p>
    </div>
  </div>
</main>
@endsection
