// Daisy Jewellery Storefront Global Script
(function () {
    const reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 1. Toast Notification Helper
    let toastTimeout;
    window.showToast = function (msg) {
        let toastEl = document.getElementById('toast');
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.id = 'toast';
            toastEl.className = 'toast';
            toastEl.setAttribute('role', 'status');
            toastEl.setAttribute('aria-live', 'polite');
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = msg;
        toastEl.classList.add('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toastEl.classList.remove('show');
        }, 2500);
    };

    // 2. Sliding Rails (Hero & Social)
    document.querySelectorAll('[data-rail]').forEach((btn) => {
        const rail = document.getElementById(btn.dataset.rail);
        if (!rail) return;
        btn.addEventListener('click', () => {
            const first = rail.firstElementChild;
            const step = first ? first.getBoundingClientRect().width + 16 : 300;
            rail.scrollBy({ left: step * Number(btn.dataset.dir), behavior: reduce ? 'auto' : 'smooth' });
        });
    });

    function syncRail(id) {
        const rail = document.getElementById(id);
        if (!rail) return;
        const btns = document.querySelectorAll(`[data-rail="${id}"]`);
        function upd() {
            const max = rail.scrollWidth - rail.clientWidth - 2;
            btns.forEach((b) => {
                const dir = Number(b.dataset.dir);
                b.disabled = dir < 0 ? rail.scrollLeft <= 2 : rail.scrollLeft >= max;
            });
        }
        rail.addEventListener('scroll', upd, { passive: true });
        window.addEventListener('resize', upd);
        upd();
    }
    syncRail('heroRail');
    syncRail('socialRail');

    // 3. Mobile Navigation Menu
    const mb = document.getElementById('menuBtn');
    const nav = document.getElementById('nav');
    const mq = window.matchMedia('(max-width: 860px)');

    function setNav() {
        if (!nav || !mb) return;
        if (mq.matches) {
            nav.hidden = true;
            mb.setAttribute('aria-expanded', 'false');
        } else {
            nav.hidden = false;
        }
    }
    setNav();
    if (mq.addEventListener) mq.addEventListener('change', setNav);

    if (mb && nav) {
        mb.addEventListener('click', () => {
            const open = nav.hidden;
            nav.hidden = !open;
            mb.setAttribute('aria-expanded', String(open));
        });
        nav.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && mq.matches) {
                nav.hidden = true;
                mb.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 4. Copy Email & Info Helper
    document.querySelectorAll('[data-copy]').forEach((b) => {
        b.addEventListener('click', () => {
            const el = document.getElementById(b.dataset.copy);
            if (!el) return;
            const text = el.textContent.trim();
            navigator.clipboard.writeText(text).then(
                () => {
                    const orig = b.textContent;
                    b.textContent = 'Copied';
                    setTimeout(() => { b.textContent = orig; }, 1800);
                },
                () => { window.showToast('Copied: ' + text); }
            );
        });
    });

    // 5. Newsletter Subscription Form AJAX
    const newsForm = document.getElementById('newsForm');
    if (newsForm) {
        newsForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = newsForm.querySelector('input[type="email"]');
            const msgEl = document.getElementById('newsMsg') || document.getElementById('msg');
            const email = emailInput?.value.trim();

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                if (msgEl) {
                    msgEl.style.color = 'var(--pink)';
                    msgEl.textContent = 'Enter a valid email address.';
                }
                return;
            }

            try {
                const res = await fetch('/newsletter/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                });

                const data = await res.json();
                if (data.success) {
                    if (msgEl) {
                        msgEl.style.color = 'var(--emerald)';
                        msgEl.textContent = data.message || 'Thank you for subscribing to Daisy!';
                    }
                    emailInput.value = '';
                    window.showToast('Subscribed to Daisy drops!');
                } else {
                    if (msgEl) {
                        msgEl.style.color = 'var(--pink)';
                        msgEl.textContent = data.message || 'Could not subscribe. Please try again.';
                    }
                }
            } catch (err) {
                if (msgEl) {
                    msgEl.style.color = 'var(--pink)';
                    msgEl.textContent = 'Subscription error. Please try again.';
                }
            }
        });
    }

    // 6. Global Wishlist Handler
    window.DaisyWishlist = {
        async toggle(productId, buttonEl) {
            try {
                const res = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId }),
                });
                const data = await res.json();
                if (data.success) {
                    const isAdded = data.added;
                    window.showToast(data.message);

                    // Update all heart icons for this product
                    document.querySelectorAll(`[data-wish-product="${productId}"]`).forEach((btn) => {
                        btn.setAttribute('aria-pressed', String(isAdded));
                        if (isAdded) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    });

                    // Update badge count
                    const badge = document.getElementById('wishCount');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.hidden = data.count === 0;
                    }
                }
            } catch (e) {
                window.showToast('Please sign in to save wishlist');
            }
        }
    };

    // 7. Global Bag Drawer & Cart Handler
    window.DaisyCart = {
        open() {
            const drawer = document.getElementById('cartDrawer');
            if (drawer) {
                this.loadDrawer();
                drawer.hidden = false;
                document.documentElement.style.overflow = 'hidden';
            }
        },
        close() {
            const drawer = document.getElementById('cartDrawer');
            if (drawer) {
                drawer.hidden = true;
                document.documentElement.style.overflow = '';
            }
        },
        async loadDrawer() {
            const body = document.getElementById('cartBody');
            if (!body) return;
            body.innerHTML = '<div style="padding:2rem;text-align:center;" class="muted">Loading your bag...</div>';
            try {
                const res = await fetch('/cart?fragment=1', {
                    headers: { 'Accept': 'text/html' },
                });
                const html = await res.text();
                body.innerHTML = html;
                this.bindDrawerEvents();
            } catch (e) {
                body.innerHTML = '<div class="empty"><h3>Could not load bag</h3><p class="muted">Please refresh the page.</p></div>';
            }
        },
        async add(productId, sizeLabel = '16', quantity = 1, openDrawer = true) {
            try {
                const res = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId, size_label: sizeLabel, quantity }),
                });
                const data = await res.json();
                if (data.success) {
                    window.showToast(data.message || 'Added to bag!');
                    this.updateCount(data.cart_count);
                    if (openDrawer) {
                        this.open();
                    }
                } else {
                    window.showToast(data.message || 'Could not add item to bag');
                }
            } catch (e) {
                window.showToast('Error adding item to bag');
            }
        },
        async update(itemId, quantity, sizeLabel) {
            try {
                const res = await fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ item_id: itemId, quantity, size_label: sizeLabel }),
                });
                const data = await res.json();
                if (data.success) {
                    this.updateCount(data.cart_count);
                    this.loadDrawer();
                } else {
                    window.showToast(data.message || 'Could not update item');
                }
            } catch (e) {
                window.showToast('Error updating bag');
            }
        },
        async remove(itemId) {
            try {
                const res = await fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ item_id: itemId }),
                });
                const data = await res.json();
                if (data.success) {
                    window.showToast('Removed from bag');
                    this.updateCount(data.cart_count);
                    this.loadDrawer();
                }
            } catch (e) {
                window.showToast('Error removing item');
            }
        },
        async applyCoupon(code) {
            try {
                const res = await fetch('/cart/apply-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ coupon_code: code }),
                });
                const data = await res.json();
                window.showToast(data.message);
                this.loadDrawer();
            } catch (e) {
                window.showToast('Error applying coupon');
            }
        },
        async removeCoupon() {
            try {
                const res = await fetch('/cart/remove-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                window.showToast(data.message);
                this.loadDrawer();
            } catch (e) {
                window.showToast('Error removing coupon');
            }
        },
        updateCount(count) {
            const badge = document.getElementById('bagCount');
            if (badge) {
                badge.textContent = count;
                badge.hidden = count === 0;
            }
        },
        bindDrawerEvents() {
            const body = document.getElementById('cartBody');
            if (!body) return;

            // Stepper minus
            body.querySelectorAll('[data-cart-act="minus"]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('[data-item-id]');
                    const id = row.dataset.itemId;
                    const qty = parseInt(row.dataset.qty, 10) - 1;
                    window.DaisyCart.update(id, qty);
                });
            });

            // Stepper plus
            body.querySelectorAll('[data-cart-act="plus"]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('[data-item-id]');
                    const id = row.dataset.itemId;
                    const qty = parseInt(row.dataset.qty, 10) + 1;
                    window.DaisyCart.update(id, qty);
                });
            });

            // Size change
            body.querySelectorAll('[data-cart-act="size"]').forEach((select) => {
                select.addEventListener('change', () => {
                    const row = select.closest('[data-item-id]');
                    const id = row.dataset.itemId;
                    window.DaisyCart.update(id, null, select.value);
                });
            });

            // Remove
            body.querySelectorAll('[data-cart-act="remove"]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('[data-item-id]');
                    const id = row.dataset.itemId;
                    window.DaisyCart.remove(id);
                });
            });

            // Coupon apply
            const couponForm = body.querySelector('#couponForm');
            if (couponForm) {
                couponForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const input = couponForm.querySelector('input[name="coupon_code"]');
                    if (input && input.value.trim()) {
                        window.DaisyCart.applyCoupon(input.value.trim());
                    }
                });
            }

            // Coupon remove
            const removeCouponBtn = body.querySelector('#removeCouponBtn');
            if (removeCouponBtn) {
                removeCouponBtn.addEventListener('click', () => {
                    window.DaisyCart.removeCoupon();
                });
            }
        }
    };

    // Attach Bag button toggle
    const bagBtn = document.getElementById('bagBtn');
    if (bagBtn) {
        bagBtn.addEventListener('click', () => window.DaisyCart.open());
    }

    // Attach Wishlist global clicks
    document.addEventListener('click', (e) => {
        const wishBtn = e.target.closest('[data-wish-product]');
        if (wishBtn) {
            e.preventDefault();
            const id = wishBtn.dataset.wishProduct;
            window.DaisyWishlist.toggle(id, wishBtn);
            return;
        }

        const addBagBtn = e.target.closest('[data-add-bag]');
        if (addBagBtn) {
            e.preventDefault();
            const id = addBagBtn.dataset.addBag;
            const size = addBagBtn.dataset.size || '16';
            window.DaisyCart.add(id, size, 1, true);
            return;
        }

        // Close overlay on background click or [data-close]
        if (e.target.matches('.overlay') || e.target.closest('[data-close]')) {
            const overlay = e.target.closest('.overlay') || e.target;
            if (overlay) {
                overlay.hidden = true;
                document.documentElement.style.overflow = '';
            }
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.overlay:not([hidden])').forEach((ov) => {
                ov.hidden = true;
            });
            document.documentElement.style.overflow = '';
        }
    });
})();
