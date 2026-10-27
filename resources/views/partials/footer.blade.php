<footer class="wrap">
  <div class="ftr">
    <div>
      <div class="big">{{ \App\Models\Setting::get('site_name', 'daisy') }}</div>
      <p class="note">{{ \App\Models\Setting::get('footer_note', 'Hallmarked 22K and 18K gold, 925 sterling silver, ethically sourced stones.') }}</p>
    </div>
    <div>
      <h3>Explore</h3>
      <ul>
        <li><a href="{{ url('/#drops') }}">New drops</a></li>
        <li><a href="{{ url('/#collections') }}">Collections</a></li>
        <li><a href="{{ route('pages.visit-custom') }}">Visit &amp; custom</a></li>
        <li><a href="{{ url('/#journal') }}">Journal</a></li>
        <li><a href="{{ route('pages.contact') }}">Contact</a></li>
        <li><a href="{{ route('rings') }}">Rings</a></li>
        <li><a href="{{ route('shop.category', 'earrings') }}">Earrings</a></li>
        <li><a href="{{ route('shop.category', 'necklaces') }}">Necklaces</a></li>
      </ul>
    </div>
    <div>
      <h3>Follow</h3>
      <ul>
        <li><a href="{{ \App\Models\Setting::get('instagram_url', 'https://www.instagram.com/') }}" target="_blank" rel="noopener">Instagram</a></li>
        <li><a href="{{ \App\Models\Setting::get('youtube_url', 'https://www.youtube.com/') }}" target="_blank" rel="noopener">YouTube</a></li>
        <li><a href="{{ \App\Models\Setting::get('pinterest_url', 'https://www.pinterest.com/') }}" target="_blank" rel="noopener">Pinterest</a></li>
        <li><a href="{{ \App\Models\Setting::get('facebook_url', 'https://www.facebook.com/') }}" target="_blank" rel="noopener">Facebook</a></li>
      </ul>
    </div>
    <div>
      <h3>Visit us</h3>
      <address>
        <span>{{ \App\Models\Setting::get('site_name', 'Daisy Jewels') }}</span>
        <span>{{ \App\Models\Setting::get('contact_address', '12 Zaveri Lane, Old Market') }}</span>
        <span>{{ \App\Models\Setting::get('store_hours', 'Mon–Sat 11:00–20:00') }}</span>
        <span>{{ \App\Models\Setting::get('contact_email', 'hello@daisyjewels.test') }}</span>
      </address>
    </div>
  </div>
  <div class="legal">
    <span>© {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Daisy') }} Jewels. All rights reserved.</span>
    <span>Prices include making charges, excluding GST.</span>
    <span style="display:flex;gap:1rem">
      <a href="{{ route('pages.about') }}">About</a>
      <a href="{{ route('pages.privacy') }}">Privacy</a>
      <a href="{{ route('pages.terms') }}">Terms</a>
    </span>
  </div>
</footer>
