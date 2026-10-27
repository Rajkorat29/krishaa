<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Dashboard') — {{ \App\Models\Setting::get('site_name', 'Daisy') }} Admin</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Figtree:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap">

  <!-- Chart.js CDN for Analytics Line Chart -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  @vite(['resources/css/admin.css', 'resources/js/admin.js'])
  @stack('styles')
</head>
<body>
  @include('partials.svg-sprite')

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="sidebar-logo">
      <a href="{{ route('admin.dashboard') }}" style="display:inline-flex;align-items:center;gap:.5rem">
        <svg aria-hidden="true"><use href="#gem"/></svg>
        <span>{{ \App\Models\Setting::get('site_name', 'daisy') }} <small style="font-size:.7rem;font-weight:600;color:var(--pink)">ADMIN</small></span>
      </a>
      <button class="btn sm menu-toggle" id="sidebarToggle" type="button" style="display:none" aria-label="Toggle menu">✕</button>
    </div>

    <nav class="sidebar-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#gem"/></svg>
        <span>Dashboard</span>
      </a>

      <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#ring"/></svg>
        <span>Products</span>
      </a>

      <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#tag"/></svg>
        <span>Categories</span>
      </a>

      <a href="{{ route('admin.metals.index') }}" class="{{ request()->routeIs('admin.metals.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#star"/></svg>
        <span>Metals</span>
      </a>

      <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#bag"/></svg>
        <span>Orders</span>
      </a>

      <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#user"/></svg>
        <span>Customers</span>
      </a>

      <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#star"/></svg>
        <span>Reviews</span>
      </a>

      <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#check"/></svg>
        <span>Coupons</span>
      </a>

      <a href="{{ route('admin.cms.index') }}" class="{{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#camera"/></svg>
        <span>Homepage CMS</span>
      </a>

      <a href="{{ route('admin.inbox.index') }}" class="{{ request()->routeIs('admin.inbox.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#arrow"/></svg>
        <span>Inbox &amp; Newsletter</span>
      </a>

      <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <svg aria-hidden="true"><use href="#edit"/></svg>
        <span>Settings</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="{{ url('/') }}" target="_blank" class="btn sm" title="Open Storefront">
        View Storefront ↗
      </a>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn sm danger" title="Sign out">
          Sign out
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:1rem">
        <h1 style="font-size:1.35rem;font-weight:700">@yield('page_title', 'Dashboard')</h1>
      </div>

      <div style="display:flex;align-items:center;gap:1rem">
        <span class="mono" style="color:var(--muted)">Signed in as <b>{{ auth()->user()->name }}</b></span>
        <a href="{{ url('/') }}" class="btn sm solid" target="_blank">Storefront ↗</a>
      </div>
    </header>

    <main class="admin-content">
      @if(session('success'))
        <div style="background:#e6f8f1;color:#0b8a64;padding:.8rem 1.4rem;border-radius:14px;margin-bottom:1.5rem;font-weight:600;border:1px solid #b7ebd7">
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div style="background:#fde8f0;color:#d81b66;padding:.8rem 1.4rem;border-radius:14px;margin-bottom:1.5rem;font-weight:600;border:1px solid #f9bdcf">
          {{ session('error') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background:#fde8f0;color:#d81b66;padding:.8rem 1.4rem;border-radius:14px;margin-bottom:1.5rem;font-weight:500;border:1px solid #f9bdcf">
          <ul style="margin:0;padding-left:1.2rem">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  @stack('scripts')
</body>
</html>
