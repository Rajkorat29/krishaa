<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'Daisy') . ' — Handcrafted Jewellery')</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Figtree:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>
  @include('partials.svg-sprite')

  @include('partials.header')

  @if(session('success'))
    <div class="wrap" style="padding-top:1rem">
      <div style="background:var(--emerald);color:var(--on-strong);padding:.8rem 1.4rem;border-radius:14px;font-weight:600">
        {{ session('success') }}
      </div>
    </div>
  @endif

  @if(session('error'))
    <div class="wrap" style="padding-top:1rem">
      <div style="background:var(--pink);color:var(--on-strong);padding:.8rem 1.4rem;border-radius:14px;font-weight:600">
        {{ session('error') }}
      </div>
    </div>
  @endif

  @yield('content')

  @include('partials.bag-drawer')
  @include('partials.detail-modal')

  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  @include('partials.footer')

  @stack('scripts')
</body>
</html>
