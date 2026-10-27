<div class="overlay side" id="cartDrawer" hidden>
  <div class="dlg" role="dialog" aria-modal="true" aria-labelledby="cartH">
    <button class="x" type="button" data-close aria-label="Close bag">
      <svg aria-hidden="true"><use href="#close"/></svg>
    </button>
    <div class="cart-h"><h2 id="cartH">Your bag</h2></div>
    <div id="cartBody" style="display:flex;flex-direction:column;gap:1rem;flex:1">
      {{-- Filled dynamically via AJAX or fragment --}}
    </div>
  </div>
</div>
