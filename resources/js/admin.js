// Daisy Admin Panel JavaScript
(function () {
    // 1. Mobile Sidebar Toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // 2. HTML Confirm Modal Helper (replacing native confirm())
    window.confirmModal = function (message, onConfirm) {
        let modal = document.getElementById('confirmModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'confirmModal';
            modal.className = 'admin-modal';
            modal.innerHTML = `
                <div class="admin-modal-box">
                    <h3 style="font-size:1.3rem;margin-bottom:.8rem">Please confirm</h3>
                    <p id="confirmModalMsg" style="color:var(--muted);margin-bottom:1.5rem"></p>
                    <div style="display:flex;gap:.8rem;justify-content:flex-end">
                        <button type="button" class="btn" id="confirmModalCancel">Cancel</button>
                        <button type="button" class="btn pink" id="confirmModalOk">Proceed</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        const msgEl = modal.querySelector('#confirmModalMsg');
        const cancelBtn = modal.querySelector('#confirmModalCancel');
        const okBtn = modal.querySelector('#confirmModalOk');

        msgEl.textContent = message;
        modal.hidden = false;

        const cleanup = () => {
            modal.hidden = true;
            cancelBtn.onclick = null;
            okBtn.onclick = null;
        };

        cancelBtn.onclick = cleanup;
        okBtn.onclick = () => {
            cleanup();
            onConfirm();
        };
    };

    // Intercept forms with data-confirm
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.hasAttribute('data-confirm') && !form.dataset.confirmed) {
            e.preventDefault();
            const message = form.getAttribute('data-confirm') || 'Are you sure you want to perform this action?';
            window.confirmModal(message, () => {
                form.dataset.confirmed = 'true';
                form.submit();
            });
        }
    });

    // 3. Auto-slug generation for forms
    const nameInput = document.getElementById('productName') || document.getElementById('categoryName');
    const slugInput = document.getElementById('productSlug') || document.getElementById('categorySlug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', () => {
            if (!slugInput.dataset.manual) {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)+/g, '');
            }
        });
        slugInput.addEventListener('input', () => {
            slugInput.dataset.manual = 'true';
        });
    }

    // 4. Multi-Image Drag and Drop with Live Preview & Cover Selection
    const imageDz = document.getElementById('imageDz');
    const imageInput = document.getElementById('productImages');
    const previewGrid = document.getElementById('previewGrid');

    if (imageDz && imageInput && previewGrid) {
        ['dragenter', 'dragover'].forEach(n => {
            imageDz.addEventListener(n, (e) => {
                e.preventDefault();
                imageDz.classList.add('over');
            });
        });

        ['dragleave', 'drop'].forEach(n => {
            imageDz.addEventListener(n, (e) => {
                e.preventDefault();
                imageDz.classList.remove('over');
            });
        });

        imageDz.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files) {
                handleFiles(e.dataTransfer.files);
            }
        });

        imageInput.addEventListener('change', () => {
            if (imageInput.files) {
                handleFiles(imageInput.files);
            }
        });

        function handleFiles(files) {
            Array.from(files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = (event) => {
                    const tile = document.createElement('div');
                    tile.className = 'image-tile';
                    tile.innerHTML = `
                        <img src="${event.target.result}" alt="Preview">
                        <span class="cover-chip" style="${previewGrid.children.length === 0 ? '' : 'display:none'}">Cover</span>
                        <div class="tile-acts">
                            <button type="button" class="btn sm" data-action="set-cover" style="padding:2px 6px;font-size:.7rem;background:var(--surface)">Cover</button>
                            <button type="button" class="btn sm danger" data-action="remove-tile" style="padding:2px 6px;font-size:.7rem;background:var(--surface)">✕</button>
                        </div>
                    `;
                    previewGrid.appendChild(tile);
                };
                reader.readAsDataURL(file);
            });
        }

        previewGrid.addEventListener('click', (e) => {
            const tile = e.target.closest('.image-tile');
            if (!tile) return;

            if (e.target.matches('[data-action="set-cover"]')) {
                previewGrid.querySelectorAll('.cover-chip').forEach(c => c.style.display = 'none');
                const chip = tile.querySelector('.cover-chip');
                if (chip) chip.style.display = '';
            }

            if (e.target.matches('[data-action="remove-tile"]')) {
                tile.remove();
            }
        });
    }

    // 5. Chart.js 30-Day Sales Chart Initializer
    const chartCanvas = document.getElementById('salesChart');
    if (chartCanvas && window.Chart) {
        const labels = JSON.parse(chartCanvas.dataset.labels || '[]');
        const values = JSON.parse(chartCanvas.dataset.values || '[]');

        new window.Chart(chartCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales (₹)',
                    data: values,
                    borderColor: '#D81B66',
                    backgroundColor: 'rgba(216, 27, 102, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#D81B66',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return '₹' + Math.round(ctx.parsed.y).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'DM Mono', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'DM Mono', size: 11 },
                            callback: function (val) {
                                return '₹' + (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val);
                            }
                        }
                    }
                }
            }
        });
    }
})();
