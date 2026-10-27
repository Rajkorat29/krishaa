<header class="hdr">
  <div class="wrap">
    <a class="logo" href="{{ url('/') }}">
      <svg aria-hidden="true"><use href="#gem"/></svg>daisy
    </a>

    <nav class="nav" id="nav" aria-label="Main">
      <a href="{{ url('/#drops') }}">New drops</a>
      <a href="{{ url('/#collections') }}">Collections</a>
      <a href="{{ route('pages.visit-custom') }}">Visit &amp; custom</a>
      <a href="{{ url('/#journal') }}">Journal</a>
      <a href="{{ route('pages.contact') }}">Contact</a>
      @auth
        <a href="{{ route('account.orders') }}">My Orders</a>
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" style="color:var(--pink);font-weight:700">Admin</a>
        @endif
      @endauth
      <a class="btn solid" href="{{ route('rings') }}">Shop rings</a>
    </nav>

    <div class="tools">
      <a href="{{ route('wishlist') }}" class="tbtn" id="wishBtn" aria-label="Wishlist">
        <svg aria-hidden="true"><use href="#heart"/></svg>
        <span class="badge" id="wishCount" {{ ($wishlistCount ?? 0) > 0 ? '' : 'hidden' }}>
          {{ $wishlistCount ?? 0 }}
        </span>
      </a>

      <button class="tbtn" id="bagBtn" type="button" aria-label="Shopping bag">
        <svg aria-hidden="true"><use href="#bag"/></svg>
        <span class="badge" id="bagCount" {{ ($cartCount ?? 0) > 0 ? '' : 'hidden' }}>
          {{ $cartCount ?? 0 }}
        </span>
      </button>

      @auth
        <a href="{{ route('account.orders') }}" class="tbtn" title="{{ auth()->user()->name }}" aria-label="Account">
          <svg aria-hidden="true"><use href="#user"/></svg>
        </a>
      @else
        <a href="{{ route('login') }}" class="tbtn" title="Sign In" aria-label="Sign In">
          <svg aria-hidden="true"><use href="#user"/></svg>
        </a>
      @endauth

      <button class="btn menu-btn" id="menuBtn" type="button" aria-expanded="false" aria-controls="nav">Menu</button>
    </div>
  </div>
</header>
