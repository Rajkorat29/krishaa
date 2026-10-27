@extends('layouts.app')

@section('title', \App\Models\Setting::get('site_name', 'Daisy') . ' — ' . \App\Models\Setting::get('tagline', 'Jewellery with a loud heart.'))

@section('content')
<main id="top">
  <!-- 1. Hero Feature Cards Slider -->
  <section class="hero wrap" id="collections" aria-label="Featured pieces">
    <div class="rail" id="heroRail" tabindex="0" aria-label="Featured pieces, scroll sideways">
      @foreach($heroSlides as $slide)
        <article class="hcard card {{ $slide->color_class }}">
          <div>
            @if($slide->tag)
              <span class="tag mono">{{ $slide->tag }}</span>
            @endif
            <h2>{{ $slide->title }}</h2>
            @if($slide->meta_text)
              <p class="meta mono">{{ $slide->meta_text }}</p>
            @endif
          </div>

          @if($slide->image)
            <img class="art" src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}">
          @else
            <svg class="art" aria-hidden="true"><use href="#{{ $slide->art_symbol }}"/></svg>
          @endif

          @php
              $slideLink = $slide->link_url;
              if (empty($slideLink) || $slideLink === '#drops') {
                  $slideLink = match($slide->art_symbol) {
                      'ring', 'ring-round', 'ring-band', 'ring-twin', 'ring-marquise' => route('rings'),
                      'earrings' => route('earrings'),
                      'bangle' => route('bangles'),
                      'necklace' => route('necklaces'),
                      'pearls', 'locket' => route('pearls'),
                      default => route('shop.index'),
                  };
              }
              $btnLabel = match($slide->art_symbol) {
                  'ring', 'ring-round', 'ring-band', 'ring-twin', 'ring-marquise' => 'Shop rings',
                  'earrings' => 'Shop earrings',
                  'bangle' => 'Shop bangles',
                  'necklace' => 'Shop necklaces',
                  'pearls', 'locket' => 'Shop pearls',
                  default => 'Shop ' . strtolower($slide->title),
              };
          @endphp
          <a class="btn {{ $loop->first ? 'stretch' : '' }}" href="{{ $slideLink }}" aria-label="Explore {{ $slide->title }}">
            {{ $btnLabel }} <svg aria-hidden="true"><use href="#arrow"/></svg>
          </a>
        </article>
      @endforeach
    </div>

    <div class="rail-ctl">
      <button class="round" data-rail="heroRail" data-dir="-1" aria-label="Previous pieces">
        <svg aria-hidden="true"><use href="#back"/></svg>
      </button>
      <button class="round" data-rail="heroRail" data-dir="1" aria-label="Next pieces">
        <svg aria-hidden="true"><use href="#arrow"/></svg>
      </button>
    </div>

    <!-- 2. Big Tagline -->
    <div class="tagline">
      <h1>Jewellery with a <em>loud</em> heart.</h1>
      <div class="side">
        <p>{{ \App\Models\Setting::get('sub_tagline', "Handmade gold, silver and gemstone pieces in colours that don't whisper.") }}</p>
        <a class="btn" href="{{ route('pages.about') }}">About us <svg aria-hidden="true"><use href="#arrow"/></svg></a>
      </div>
    </div>
  </section>

  <!-- 3. New Drops List -->
  <section class="wrap" id="drops" aria-labelledby="dropsH">
    <div class="sec-h">
      <h2 id="dropsH">New drops</h2>
      <span class="mono" style="color:var(--muted)">Exclusive pieces, weights and prices</span>
    </div>
    <ul class="drops">
      @foreach($drops as $drop)
        <li class="drop">
          <span class="date">{{ $drop->drop_date }}</span>
          <span class="time">{{ $drop->drop_time }}</span>
          <span class="metal">
            <i class="dot" style="--c:var(--gold)"></i>{{ $drop->metal_label }}
          </span>
          <h3>{{ $drop->title }}<small>{{ $drop->subtitle }}</small></h3>
          <span class="wt">{{ $drop->weight_label }}</span>
          <span class="price">{{ $drop->price_label }}</span>

          <span class="meta-line">
            <span>{{ $drop->drop_time }}</span>
            <span>{{ $drop->metal_label }}</span>
            <span>{{ $drop->weight_label }}</span>
            <span>{{ $drop->price_label }}</span>
          </span>

          @if($drop->status === 'sold_out')
            <span class="sold act">Sold out</span>
          @else
            <a class="btn act" href="{{ $drop->product_id ? route('product.show', $drop->product_id) : route('rings') }}">Shop</a>
          @endif
        </li>
      @endforeach
    </ul>
  </section>

  <!-- 4. Follow Daisy Social Rail -->
  <section class="wrap social" id="journal" aria-labelledby="folH">
    <div class="sec-h">
      <h2 id="folH">Follow daisy</h2>
      <div class="rail-ctl" style="margin:0">
        <button class="round" data-rail="socialRail" data-dir="-1" aria-label="Previous posts">
          <svg aria-hidden="true"><use href="#back"/></svg>
        </button>
        <button class="round" data-rail="socialRail" data-dir="1" aria-label="Next posts">
          <svg aria-hidden="true"><use href="#arrow"/></svg>
        </button>
      </div>
    </div>
    <div class="rail social-rail" id="socialRail" tabindex="0" aria-label="Latest posts, scroll sideways">
      @foreach($socialPosts as $post)
        <article class="tile card {{ $post->color_class }}">
          <span class="chip mono">{{ $post->platform_label }}</span>
          @if($post->image)
            <img class="art" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->caption }}">
          @else
            <svg class="art" aria-hidden="true"><use href="#{{ $post->art_symbol }}"/></svg>
          @endif
          <p>{{ $post->caption }}</p>
        </article>
      @endforeach
    </div>
  </section>

  <!-- 5. Marquee Words -->
  <section class="words" aria-labelledby="wearH">
    <div class="wrap"><h2 id="wearH">Wear it as you are</h2></div>
    <div class="marquee" aria-label="{{ implode(', ', $marqueeWords->pluck('word')->toArray()) }}">
      <div class="track">
        <div class="group">
          @foreach($marqueeWords as $idx => $mw)
            <span>{{ $mw->word }}</span>
            <svg aria-hidden="true" style="color:var({{ ['--gold', '--pink', '--emerald', '--sapphire'][$idx % 4] }})">
              <use href="#gem"/>
            </svg>
          @endforeach
        </div>
        <div class="group" aria-hidden="true">
          @foreach($marqueeWords as $idx => $mw)
            <span>{{ $mw->word }}</span>
            <svg style="color:var({{ ['--gold', '--pink', '--emerald', '--sapphire'][$idx % 4] }})">
              <use href="#gem"/>
            </svg>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Split Cards -->
  <section class="wrap split" id="visit" aria-label="Visit and custom orders">
    <article class="sp">
      <div class="panel card c-gold">
        <span class="tag mono">In the store</span>
        <svg class="art" aria-hidden="true"><use href="#locket"/></svg>
      </div>
      <div class="body">
        <h3>{{ \App\Models\Setting::get('split_card_1_title', 'Try on, touch, take home') }}</h3>
        <p>{{ \App\Models\Setting::get('split_card_1_text', 'Meet the makers, feel the weight of a piece and get it sized on the spot. Every gold piece carries a BIS hallmark.') }}</p>
        <a class="btn" href="{{ route('pages.visit-custom') }}">Read more <svg aria-hidden="true"><use href="#arrow"/></svg></a>
      </div>
    </article>

    <article class="sp">
      <div class="panel card c-emerald">
        <span class="tag mono">Custom orders</span>
        <svg class="art" aria-hidden="true"><use href="#ring"/></svg>
      </div>
      <div class="body">
        <h3>{{ \App\Models\Setting::get('split_card_2_title', 'Design your own piece') }}</h3>
        <p>{{ \App\Models\Setting::get('split_card_2_text', 'Bring a sketch, a stone or a family heirloom. Our goldsmiths turn it into something new, with a quote before work begins.') }}</p>
        <a class="btn" href="{{ route('pages.visit-custom') }}">Read more <svg aria-hidden="true"><use href="#arrow"/></svg></a>
      </div>
    </article>
  </section>

  <!-- 7. Contact Block -->
  <section class="wrap" id="contact" aria-labelledby="contH">
    <div class="contact card c-pink">
      <div>
        <h2 id="contH">We'd love to hear from you.</h2>
        <p>Questions about a piece, a size, a repair or a commission? Write or call and we'll help.</p>
        <a class="btn" href="{{ route('pages.contact') }}">Contact us <svg aria-hidden="true"><use href="#arrow"/></svg></a>
        <div class="reach">
          <span class="sel" id="mail">{{ \App\Models\Setting::get('contact_email', 'hello@daisyjewels.test') }}</span>
          <button class="copy" type="button" data-copy="mail">Copy</button>
          <span class="sel">{{ \App\Models\Setting::get('contact_phone', '+91 98765 43210') }}</span>
        </div>
      </div>
      <div class="art-wrap">
        <svg class="art" aria-hidden="true"><use href="#earrings"/></svg>
      </div>
    </div>
  </section>

  <!-- 8. Newsletter Subscription -->
  <section class="wrap news" aria-labelledby="newsH">
    <h2 id="newsH">Never miss a drop.</h2>
    <form id="newsForm" novalidate>
      @csrf
      <label class="sr" for="newsEmail">Email address</label>
      <input id="newsEmail" type="email" placeholder="you@example.com" autocomplete="email" required>
      <button class="btn solid" type="submit">Join the list</button>
      <p class="msg" id="newsMsg" role="status"></p>
    </form>
  </section>
</main>
@endsection
