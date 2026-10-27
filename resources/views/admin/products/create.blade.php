@extends('layouts.admin')

@section('page_title', 'Add New Piece')

@section('content')
<div class="admin-card">
  <div class="admin-card-h">
    <h2>Product Details</h2>
    <a href="{{ route('admin.products.index') }}" class="btn sm">← Back to pieces</a>
  </div>

  <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="form">
    @csrf

    <div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:2rem">
      <!-- Left Column: Core Info & Multi-Image Upload -->
      <div style="display:grid;gap:1.2rem">
        <div class="fld">
          <label for="productName">Piece Name *</label>
          <input class="in" type="text" id="productName" name="name" value="{{ old('name') }}" placeholder="e.g. Lotus Solitaire" required>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="productSlug">Slug (Auto-generated)</label>
            <input class="in" type="text" id="productSlug" name="slug" value="{{ old('slug') }}" placeholder="lotus-solitaire">
          </div>
          <div class="fld">
            <label for="productSku">SKU (Auto if blank)</label>
            <input class="in" type="text" id="productSku" name="sku" value="{{ old('sku') }}" placeholder="DSY-XXXXXX">
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="pCategory">Category *</label>
            <select name="category_id" id="pCategory" class="in" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="fld">
            <label for="pMetal">Metal *</label>
            <select name="metal_id" id="pMetal" class="in" required>
              @foreach($metals as $m)
                <option value="{{ $m->id }}" {{ old('metal_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="pPrice">Price (₹) *</label>
            <input class="in" type="number" step="0.01" id="pPrice" name="price" value="{{ old('price') }}" placeholder="38400" required>
          </div>
          <div class="fld">
            <label for="pMrp">MRP (₹, Optional strikethrough)</label>
            <input class="in" type="number" step="0.01" id="pMrp" name="mrp" value="{{ old('mrp') }}" placeholder="45000">
          </div>
        </div>

        <div class="row2">
          <div class="fld">
            <label for="pWeight">Weight in Grams</label>
            <input class="in" type="number" step="0.01" id="pWeight" name="weight_grams" value="{{ old('weight_grams', '0') }}">
          </div>
          <div class="fld">
            <label for="pStone">Stone / Accents</label>
            <input class="in" type="text" id="pStone" name="stone" value="{{ old('stone') }}" placeholder="e.g. Ruby accents, Lab diamond 0.5 ct">
          </div>
        </div>

        <div class="fld">
          <label for="pShortDesc">Short Summary</label>
          <input class="in" type="text" id="pShortDesc" name="short_description" value="{{ old('short_description') }}" placeholder="Brief 1-line headline">
        </div>

        <div class="fld">
          <label for="pDesc">Full Description</label>
          <textarea class="in" id="pDesc" name="description" rows="4" placeholder="Detailed craft notes, finish, occasion, hallmarking info...">{{ old('description') }}</textarea>
        </div>

        <!-- Multi-Image Upload Area -->
        <div class="fld">
          <label>Photos (Multi-upload with drag-and-drop &amp; preview)</label>
          <div class="image-dz" id="imageDz">
            <svg aria-hidden="true" style="width:2.2rem;height:2.2rem;color:var(--muted);margin-bottom:.5rem"><use href="#camera"/></svg>
            <p style="margin:0;font-weight:600">Drag &amp; drop photos here, or click to browse</p>
            <small class="muted">First uploaded photo is set as the cover image.</small>
            <input type="file" name="images[]" id="productImages" multiple accept="image/*" style="display:none">
          </div>
          <div class="image-preview-grid" id="previewGrid"></div>
        </div>
      </div>

      <!-- Right Column: Settings, Sizes, Fallback Artwork -->
      <div style="display:grid;gap:1.2rem">
        <!-- Stock & Status -->
        <div class="admin-card" style="margin-bottom:0;background:var(--bg)">
          <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:1rem">Inventory &amp; Visibility</h3>

          <div class="fld" style="margin-bottom:1rem">
            <label for="pStock">Total Stock Quantity *</label>
            <input class="in" type="number" id="pStock" name="stock" value="{{ old('stock', 10) }}" min="0" required>
          </div>

          <label class="check" style="margin-bottom:.8rem">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
            <strong>Active in storefront</strong>
          </label>

          <label class="check">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <strong>Featured on homepage collection rail</strong>
          </label>
        </div>

        <!-- Ring Size Stock Breakdown Grid -->
        <div class="admin-card" style="margin-bottom:0;background:var(--bg)">
          <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:.5rem">Ring Sizes Stock (India Sizes)</h3>
          <p class="muted" style="font-size:.85rem;margin-bottom:1rem">Optional individual stock per ring size.</p>

          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.6rem">
            @foreach($ringSizes as $sz)
              <div>
                <label class="mono" style="font-size:.78rem">Size {{ $sz }}</label>
                <input class="in" type="number" name="sizes[{{ $sz }}]" value="{{ old("sizes.{$sz}", 5) }}" min="0" style="padding:.4rem .6rem">
              </div>
            @endforeach
          </div>
        </div>

        <!-- SVG Fallback Artwork & Color Class -->
        <div class="admin-card" style="margin-bottom:0;background:var(--bg)">
          <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:1rem">Placeholder Artwork &amp; Color</h3>

          <div class="fld" style="margin-bottom:1rem">
            <label for="artSymbol">SVG Fallback Symbol</label>
            <select name="art_symbol" id="artSymbol" class="in" required>
              @foreach($symbols as $sym)
                <option value="{{ $sym }}" {{ old('art_symbol') == $sym ? 'selected' : '' }}>{{ $sym }}</option>
              @endforeach
            </select>
          </div>

          <div class="fld">
            <label for="colorClass">Card Color Theme</label>
            <select name="color_class" id="colorClass" class="in" required>
              @foreach($colorClasses as $col)
                <option value="{{ $col }}" {{ old('color_class') == $col ? 'selected' : '' }}>{{ $col }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>

    <div style="margin-top:2rem;display:flex;gap:1rem">
      <button type="submit" class="btn solid" style="padding:.9rem 2rem">Save piece</button>
      <a href="{{ route('admin.products.index') }}" class="btn">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('imageDz').addEventListener('click', function () {
    document.getElementById('productImages').click();
});
</script>
@endpush
