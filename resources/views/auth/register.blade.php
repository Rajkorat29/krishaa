<x-guest-layout>
  <h2 style="font-size:1.6rem;font-weight:800;letter-spacing:-.03em;margin-bottom:1.2rem">Create account</h2>

  @if($errors->any())
    <div style="background:#fde8f0;color:#d81b66;padding:.75rem 1rem;border-radius:12px;margin-bottom:1rem;font-size:.9rem">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}" class="form">
    @csrf

    <div class="fld">
      <label for="name">Full name</label>
      <input class="in" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
    </div>

    <div class="fld">
      <label for="email">Email address</label>
      <input class="in" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
    </div>

    <div class="fld">
      <label for="phone">Mobile number (Optional)</label>
      <input class="in" id="phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="10" placeholder="10-digit mobile">
    </div>

    <div class="fld">
      <label for="password">Password</label>
      <input class="in" id="password" type="password" name="password" required autocomplete="new-password">
    </div>

    <div class="fld">
      <label for="password_confirmation">Confirm password</label>
      <input class="in" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>

    <button type="submit" class="btn solid" style="justify-content:center;padding:.9rem 1.4rem;font-size:1rem;margin-top:.4rem">
      Create account
    </button>
  </form>

  <div style="margin-top:1.5rem;border-top:1px solid var(--line);padding-top:1.2rem;text-align:center;font-size:.92rem">
    <span class="muted">Already have an account?</span>
    <a href="{{ route('login') }}" style="font-weight:700;color:var(--pink);margin-left:.3rem">Sign in</a>
  </div>
</x-guest-layout>
