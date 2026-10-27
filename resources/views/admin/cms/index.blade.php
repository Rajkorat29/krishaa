@extends('layouts.admin')

@section('page_title', 'Homepage CMS')

@section('content')
<div style="display:grid;gap:2rem">

  <!-- 1. Hero Feature Slides -->
  <div class="admin-card">
    <div class="admin-card-h">
      <div>
        <h2>1. Hero Feature Slider</h2>
        <span class="muted" style="font-size:.88rem">Displayed in the top horizontal slider on the homepage.</span>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;margin-bottom:1.5rem">
      @foreach($heroSlides as $slide)
        <div class="admin-card" style="margin-bottom:0;background:var(--bg)">
          <div style="display:flex;justify-content:space-between;align-items:flex-start">
            <span class="badge {{ $slide->is_active ? 'success' : 'muted' }}">{{ $slide->tag ?: 'No Tag' }}</span>
            <form action="{{ route('admin.cms.hero.delete', $slide->id) }}" method="POST" data-confirm="Delete hero slide '{{ $slide->title }}'?">
              @csrf
              @method('DELETE')
              <button type="submit" class="linkbtn" style="color:var(--pink)">Delete</button>
            </form>
          </div>
          <h3 style="font-size:1.15rem;font-weight:700;margin:.5rem 0 .2rem">{{ $slide->title }}</h3>
          <p class="mono" style="margin:0 0 .5rem;color:var(--muted);font-size:.8rem">{{ $slide->meta_text }}</p>
          <small class="mono">Symbol: {{ $slide->art_symbol }} · Class: {{ $slide->color_class }}</small>
        </div>
      @endforeach
    </div>

    <!-- Add Hero Slide Form -->
    <details style="background:var(--bg);border:1px solid var(--line);border-radius:16px;padding:1rem">
      <summary style="font-weight:700;cursor:pointer">＋ Add New Hero Slide</summary>
      <form action="{{ route('admin.cms.hero.save') }}" method="POST" enctype="multipart/form-data" class="form" style="margin-top:1rem">
        @csrf
        <div class="row2">
          <div class="fld">
            <label>Badge Tag</label>
            <input class="in" type="text" name="tag" placeholder="e.g. Next · 12 Dec">
          </div>
          <div class="fld">
            <label>Title *</label>
            <input class="in" type="text" name="title" placeholder="e.g. Navratna Choker" required>
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Subtitle / Meta Text</label>
            <input class="in" type="text" name="meta_text" placeholder="e.g. 22K gold · ₹84,000">
          </div>
          <div class="fld">
            <label>Link URL</label>
            <input class="in" type="text" name="link_url" placeholder="/rings or #drops">
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Artwork Symbol</label>
            <select name="art_symbol" class="in">
              @foreach($symbols as $sym)
                <option value="{{ $sym }}">{{ $sym }}</option>
              @endforeach
            </select>
          </div>
          <div class="fld">
            <label>Color Theme</label>
            <select name="color_class" class="in">
              @foreach($colorClasses as $col)
                <option value="{{ $col }}">{{ $col }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Custom Photo (Optional override)</label>
            <input class="in" type="file" name="image" accept="image/*">
          </div>
          <div class="fld">
            <label>Sort Order</label>
            <input class="in" type="number" name="sort_order" value="10">
          </div>
        </div>

        <label class="check">
          <input type="checkbox" name="is_active" value="1" checked> Active on storefront
        </label>

        <button type="submit" class="btn solid" style="justify-self:start">Save Hero Slide</button>
      </form>
    </details>
  </div>

  <!-- 2. New Drops List -->
  <div class="admin-card">
    <div class="admin-card-h">
      <div>
        <h2>2. New Drops Timeline</h2>
        <span class="muted" style="font-size:.88rem">Scheduled jewellery drops shown on the homepage.</span>
      </div>
    </div>

    <div class="table-wrap" style="margin-bottom:1.5rem">
      <table>
        <thead>
          <tr>
            <th>Date &amp; Time</th>
            <th>Title &amp; Subtitle</th>
            <th>Metal</th>
            <th>Weight</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($drops as $d)
            <tr>
              <td>
                <b>{{ $d->drop_date }}</b><br>
                <small class="mono" style="color:var(--muted)">{{ $d->drop_time }}</small>
              </td>
              <td>
                <strong>{{ $d->title }}</strong><br>
                <small class="muted">{{ $d->subtitle }}</small>
              </td>
              <td>{{ $d->metal_label }}</td>
              <td class="mono">{{ $d->weight_label }}</td>
              <td class="mono"><b>{{ $d->price_label }}</b></td>
              <td>
                <span class="badge {{ $d->status === 'available' ? 'success' : 'danger' }}">
                  {{ $d->status === 'available' ? 'Available' : 'Sold Out' }}
                </span>
              </td>
              <td>
                <form action="{{ route('admin.cms.drop.delete', $d->id) }}" method="POST" data-confirm="Delete drop '{{ $d->title }}'?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Add Drop Form -->
    <details style="background:var(--bg);border:1px solid var(--line);border-radius:16px;padding:1rem">
      <summary style="font-weight:700;cursor:pointer">＋ Add New Scheduled Drop</summary>
      <form action="{{ route('admin.cms.drop.save') }}" method="POST" class="form" style="margin-top:1rem">
        @csrf
        <div class="row2">
          <div class="fld">
            <label>Piece Title *</label>
            <input class="in" type="text" name="title" placeholder="e.g. Chaand Drop Earrings" required>
          </div>
          <div class="fld">
            <label>Subtitle / Description</label>
            <input class="in" type="text" name="subtitle" placeholder="e.g. Teardrop gold with a hand-set ruby">
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Drop Date *</label>
            <input class="in" type="text" name="drop_date" placeholder="e.g. 15 Dec 2026" required>
          </div>
          <div class="fld">
            <label>Drop Time *</label>
            <input class="in" type="text" name="drop_time" value="11:00" required>
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Metal Label</label>
            <input class="in" type="text" name="metal_label" placeholder="e.g. 22K gold">
          </div>
          <div class="fld">
            <label>Weight Label</label>
            <input class="in" type="text" name="weight_label" placeholder="e.g. 8.4 g">
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label>Price Label</label>
            <input class="in" type="text" name="price_label" placeholder="e.g. ₹61,200">
          </div>
          <div class="fld">
            <label>Availability Status *</label>
            <select name="status" class="in">
              <option value="available">Available</option>
              <option value="sold_out">Sold Out</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn solid" style="justify-self:start">Save Drop</button>
      </form>
    </details>
  </div>

  <!-- 3. Social Posts & 4. Marquee Words -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem">
    <!-- Social Posts -->
    <div class="admin-card">
      <div class="admin-card-h">
        <h2>3. Social Rail Posts</h2>
      </div>

      <div style="display:grid;gap:.8rem;max-height:350px;overflow-y:auto;margin-bottom:1rem">
        @foreach($socialPosts as $sp)
          <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem;background:var(--bg);border-radius:12px">
            <div>
              <span class="mono" style="font-size:.78rem;color:var(--pink)">{{ $sp->platform_label }}</span>
              <p style="margin:.2rem 0 0;font-weight:600">{{ $sp->caption }}</p>
            </div>
            <form action="{{ route('admin.cms.social.delete', $sp->id) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="linkbtn" style="color:var(--pink)">✕</button>
            </form>
          </div>
        @endforeach
      </div>

      <form action="{{ route('admin.cms.social.save') }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label>Platform Label *</label>
          <input class="in" type="text" name="platform_label" placeholder="Instagram · Reel" required>
        </div>
        <div class="fld">
          <label>Caption *</label>
          <input class="in" type="text" name="caption" placeholder="Caption..." required>
        </div>
        <div class="row2">
          <div class="fld">
            <label>Artwork</label>
            <select name="art_symbol" class="in">
              @foreach($symbols as $sym)
                <option value="{{ $sym }}">{{ $sym }}</option>
              @endforeach
            </select>
          </div>
          <div class="fld">
            <label>Theme</label>
            <select name="color_class" class="in">
              @foreach($colorClasses as $col)
                <option value="{{ $col }}">{{ $col }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <button type="submit" class="btn solid" style="justify-self:start">Add Social Post</button>
      </form>
    </div>

    <!-- Marquee Words -->
    <div class="admin-card">
      <div class="admin-card-h">
        <h2>4. Word Marquee</h2>
      </div>

      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem">
        @foreach($marqueeWords as $mw)
          <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.4rem .9rem;background:var(--bg);border-radius:999px;border:1px solid var(--line)">
            <strong>{{ $mw->word }}</strong>
            <form action="{{ route('admin.cms.marquee.delete', $mw->id) }}" method="POST" style="margin:0">
              @csrf
              @method('DELETE')
              <button type="submit" style="background:none;border:0;cursor:pointer;color:var(--pink);font-weight:700">✕</button>
            </form>
          </div>
        @endforeach
      </div>

      <form action="{{ route('admin.cms.marquee.save') }}" method="POST" class="form">
        @csrf
        <div class="fld">
          <label for="mWord">Add Word *</label>
          <input class="in" type="text" id="mWord" name="word" placeholder="e.g. Sculptural" required>
        </div>
        <button type="submit" class="btn solid" style="justify-self:start">Add Word</button>
      </form>
    </div>
  </div>

</div>
@endsection
