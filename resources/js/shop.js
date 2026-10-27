// Daisy Shop & Listing Dynamic Filter Script
(function () {
    const grid = document.getElementById('grid');
    if (!grid) return; // Only run on listing pages

    const priceSlider = document.getElementById('priceMax');
    const priceLbl = document.getElementById('priceLbl');
    const searchInput = document.getElementById('q');
    const sortSelect = document.getElementById('sort');
    const filterPanel = document.getElementById('filters');
    const filterBtn = document.getElementById('filterBtn');
    const clearBtn = document.getElementById('clearF');
    const resultCount = document.getElementById('resultCount');
    const emptyState = document.getElementById('empty');
    const detailOverlay = document.getElementById('detail');
    const detailBody = document.getElementById('detailBody');

    // Mobile filter toggle
    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', () => {
            const isOpen = filterPanel.classList.toggle('open');
            filterBtn.setAttribute('aria-expanded', String(isOpen));
        });
    }

    // Format INR
    function formatInr(num) {
        return '₹' + Math.round(num).toLocaleString('en-IN');
    }

    // Price Slider label update
    if (priceSlider && priceLbl) {
        priceSlider.addEventListener('input', () => {
            priceLbl.textContent = 'Up to ' + formatInr(priceSlider.value);
        });
    }

    // Debounce helper
    function debounce(fn, ms = 300) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), ms);
        };
    }

    // Collect filter state
    function getFilterParams() {
        const params = new URLSearchParams();

        if (searchInput && searchInput.value.trim()) {
            params.set('q', searchInput.value.trim());
        }

        if (sortSelect && sortSelect.value) {
            params.set('sort', sortSelect.value);
        }

        if (priceSlider && priceSlider.value < 120000) {
            params.set('max_price', priceSlider.value);
        }

        const checkedMetals = Array.from(document.querySelectorAll('#metalF input:checked')).map(i => i.value);
        if (checkedMetals.length) {
            params.set('metals', checkedMetals.join(','));
        }

        const ratingInput = document.querySelector('input[name="rate"]:checked');
        if (ratingInput && Number(ratingInput.value) > 0) {
            params.set('rating', ratingInput.value);
        }

        const wishOnly = document.getElementById('wishOnly');
        if (wishOnly && wishOnly.checked) {
            params.set('wish_only', '1');
        }

        const inStock = document.getElementById('inStock');
        if (inStock && inStock.checked) {
            params.set('in_stock', '1');
        }

        return params;
    }

    // Fetch and render filtered grid
    async function applyFilters() {
        const params = getFilterParams();
        const currentUrl = new URL(window.location.href);
        const fetchUrl = `${currentUrl.pathname}?${params.toString()}`;

        // Update browser URL without reload
        window.history.pushState({}, '', fetchUrl);

        try {
            grid.style.opacity = '0.5';
            const res = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();

            grid.innerHTML = data.html;
            grid.style.opacity = '1';

            if (resultCount) {
                resultCount.textContent = `Showing ${data.count} of ${data.total} pieces`;
            }

            if (emptyState) {
                emptyState.hidden = data.count > 0;
            }
        } catch (e) {
            grid.style.opacity = '1';
        }
    }

    const debouncedApply = debounce(applyFilters, 250);

    // Event listeners
    if (searchInput) searchInput.addEventListener('input', debouncedApply);
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);
    if (priceSlider) priceSlider.addEventListener('change', applyFilters);

    if (filterPanel) {
        filterPanel.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach((input) => {
            input.addEventListener('change', applyFilters);
        });
    }

    // Clear filters
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (priceSlider) {
                priceSlider.value = 120000;
                if (priceLbl) priceLbl.textContent = 'Up to ₹1,20,000';
            }
            if (sortSelect) sortSelect.value = 'pop';
            document.querySelectorAll('#metalF input').forEach(i => i.checked = false);
            const rate0 = document.querySelector('input[name="rate"][value="0"]');
            if (rate0) rate0.checked = true;
            const wishOnly = document.getElementById('wishOnly');
            if (wishOnly) wishOnly.checked = false;
            const inStock = document.getElementById('inStock');
            if (inStock) inStock.checked = false;
            applyFilters();
        });
    }

    // Quick View Detail Modal
    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-open-detail]');
        if (!trigger) return;
        e.preventDefault();

        const slugOrId = trigger.dataset.openDetail;
        if (!detailOverlay || !detailBody) return;

        detailBody.innerHTML = '<div style="padding:3rem;text-align:center;" class="muted">Loading product details...</div>';
        detailOverlay.hidden = false;
        document.documentElement.style.overflow = 'hidden';

        try {
            const res = await fetch(`/product/${slugOrId}?modal=1`, {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const html = await res.text();
            detailBody.innerHTML = html;
            bindDetailModalEvents();
        } catch (err) {
            detailBody.innerHTML = '<div class="empty"><h3>Could not load product</h3><p class="muted">Please try again.</p></div>';
        }
    });

    function bindDetailModalEvents() {
        if (!detailBody) return;

        // Size selector
        let selectedSize = '16';
        const defaultSizeBtn = detailBody.querySelector('.sz[aria-checked="true"]') || detailBody.querySelector('.sz');
        if (defaultSizeBtn) {
            selectedSize = defaultSizeBtn.dataset.size;
        }

        detailBody.querySelectorAll('.sz').forEach((btn) => {
            btn.addEventListener('click', () => {
                detailBody.querySelectorAll('.sz').forEach(b => b.setAttribute('aria-checked', 'false'));
                btn.setAttribute('aria-checked', 'true');
                selectedSize = btn.dataset.size;
            });
        });

        // Photo thumbnail switcher
        detailBody.querySelectorAll('[data-thumb-src]').forEach((btn) => {
            btn.addEventListener('click', () => {
                detailBody.querySelectorAll('[data-thumb-src]').forEach(b => b.removeAttribute('aria-current'));
                btn.setAttribute('aria-current', 'true');
                const mainImg = detailBody.querySelector('#dMainImg');
                if (mainImg) {
                    mainImg.src = btn.dataset.thumbSrc;
                }
            });
        });

        // Pincode delivery check
        const pinBtn = detailBody.querySelector('[data-d="pin"]');
        const pinInput = detailBody.querySelector('#pin');
        const pinMsg = detailBody.querySelector('#pinMsg');

        if (pinBtn && pinInput && pinMsg) {
            pinBtn.addEventListener('click', () => {
                const val = pinInput.value.trim();
                if (/^[1-9][0-9]{5}$/.test(val)) {
                    const est = new Date();
                    est.setDate(est.getDate() + 4);
                    const formatted = est.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' });
                    pinMsg.className = 'pinmsg';
                    pinMsg.textContent = `Estimated delivery by ${formatted}. Free standard delivery.`;
                } else {
                    pinMsg.className = 'pinmsg bad';
                    pinMsg.textContent = 'Enter a valid 6-digit Indian pincode.';
                }
            });
        }

        // Add to Bag from modal
        const addBtn = detailBody.querySelector('[data-modal-act="add"]');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const prodId = addBtn.dataset.productId;
                window.DaisyCart.add(prodId, selectedSize, 1, true);
            });
        }

        // Buy Now from modal
        const buyBtn = detailBody.querySelector('[data-modal-act="buy"]');
        if (buyBtn) {
            buyBtn.addEventListener('click', async () => {
                const prodId = buyBtn.dataset.productId;
                await window.DaisyCart.add(prodId, selectedSize, 1, false);
                window.location.href = '/checkout';
            });
        }
    }
})();
