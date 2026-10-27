<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Daisy') }} — Account</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Figtree:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--bg);color:var(--fg);min-height:100vh;display:grid;place-items:center;padding:2rem 1rem">
  @include('partials.svg-sprite')

  <div style="width:min(440px,100%);display:grid;gap:1.5rem">
    <div style="text-align:center">
      <a class="logo" href="{{ url('/') }}" style="font-size:2.2rem">
        <svg aria-hidden="true" style="width:2rem;height:2rem;color:var(--pink)"><use href="#gem"/></svg>daisy
      </a>
      <p class="mono" style="margin:.4rem 0 0;color:var(--muted)">Jewellery with a loud heart</p>
    </div>

    <div class="page-box" style="margin:0;box-shadow:0 10px 30px rgb(0 0 0 / .07);border-radius:26px">
      {{ $slot }}
    </div>

    <div style="text-align:center">
      <a href="{{ url('/') }}" class="linkbtn">← Back to Daisy storefront</a>
    </div>
  </div>
</body>
</html>
