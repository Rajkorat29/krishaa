<x-guest-layout>
  <h2 style="font-size:1.6rem;font-weight:800;letter-spacing:-.03em;margin-bottom:1.2rem">Sign in</h2>

  @if(session('error'))
    <div style="background:#fde8f0;color:#d81b66;padding:.75rem 1rem;border-radius:12px;margin-bottom:1rem;font-size:.9rem">
      {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#fde8f0;color:#d81b66;padding:.75rem 1rem;border-radius:12px;margin-bottom:1rem;font-size:.9rem">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="form">
    @csrf

    <div class="fld">
      <label for="email">Email address</label>
      <input class="in" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
    </div>

    <div class="fld">
      <div style="display:flex;justify-content:space-between;align-items:baseline">
        <label for="password">Password</label>
        @if (Route::has('password.request'))
          <a class="linkbtn" href="{{ route('password.request') }}" style="font-size:.82rem">Forgot?</a>
        @endif
      </div>
      <input class="in" id="password" type="password" name="password" required autocomplete="current-password">
    </div>

    <label class="check">
      <input type="checkbox" name="remember" checked> Keep me signed in
    </label>

    <button type="submit" class="btn solid" style="justify-content:center;padding:.9rem 1.4rem;font-size:1rem;margin-top:.4rem">
      Sign in to Daisy
    </button>
  </form>

  <div style="margin-top:1.5rem;border-top:1px solid var(--line);padding-top:1.2rem;text-align:center;font-size:.92rem">
    <span class="muted">Don't have an account?</span>
    <a href="{{ route('register') }}" style="font-weight:700;color:var(--pink);margin-left:.3rem">Create an account</a>
  </div>

  <div style="margin-top:1rem;background:var(--bg);border:1px solid var(--line);border-radius:14px;padding:.8rem;font-size:.82rem">
    <strong class="mono" style="color:var(--muted)">Demo Credentials:</strong><br>
    Admin: <code class="mono">admin@daisy.test</code> / <code class="mono">Admin@12345</code><br>
    Customer: <code class="mono">customer@daisy.test</code> / <code class="mono">Customer@12345</code>
  </div>
</x-guest-layout>
