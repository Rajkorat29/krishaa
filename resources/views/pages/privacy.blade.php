@extends('layouts.app')

@section('title', 'Privacy Policy — ' . \App\Models\Setting::get('site_name', 'Daisy'))

@section('content')
<main class="wrap" style="padding-top:1.5rem;padding-bottom:3rem;max-width:850px">
  <p class="crumbs mono" style="margin-bottom:1.5rem">
    <a href="{{ url('/') }}">Home</a> / Privacy Policy
  </p>

  <div class="page-box">
    <h1 class="page-title">Privacy Policy</h1>
    <div class="content-text">
      <p>Last updated: {{ date('F Y') }}</p>
      <p>At <strong>Daisy</strong>, we are committed to safeguarding your personal data and ensuring transparent privacy practices. This policy outlines how we collect, store, and process your information when you visit our website or make purchases.</p>
      <h3 style="font-size:1.3rem;font-weight:700;margin:1.5rem 0 .5rem">1. Data We Collect</h3>
      <p>We collect personal information that you provide when registering an account, placing an order, or contacting us, including your name, email, phone number, and delivery address. Payment credentials are processed directly through secure PCI-DSS compliant payment gateways.</p>
      <h3 style="font-size:1.3rem;font-weight:700;margin:1.5rem 0 .5rem">2. How We Use Your Information</h3>
      <p>Your details are used solely to process orders, arrange insured shipment, communicate order status updates, and respond to your design inquiries.</p>
    </div>
  </div>
</main>
@endsection
