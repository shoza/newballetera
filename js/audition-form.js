const CHUNK_SIZE = 512 * 1024; // 512 KB per chunk
const STRIPE_PK  = 'pk_live_51P8mzo2LGldM45CFs1zxLxK6QxmDBfMG84wM2UtbYdgYaZnStMZUOnNQzZuCM7NKtOgA5s9vFqvyIXsPznnvHNEs00FRHnYQiN';
const APP_FEE    = document.querySelector('meta[name="app-fee"]')?.content ?? '25.00';

// ---- Stripe payment ----
let _stripe    = null;
let _stripeEl  = null;   // Stripe Elements instance
let _pendingId = null;
let _pendingType = null;

function showPaymentSection(clientSecret, appId, appType) {
    _pendingId   = appId;
    _pendingType = appType;

    const section = document.getElementById('payment-section');
    if (!section) return;

    if (!window.Stripe) {
        document.getElementById('payment-error').textContent = 'Stripe.js failed to load. Please refresh.';
        section.style.display = 'block';
        return;
    }

    _stripe   = Stripe(STRIPE_PK);
    _stripeEl = _stripe.elements({
        clientSecret,
        appearance: { theme: 'stripe', variables: { borderRadius: '2px' } },
    });

    const paymentEl = _stripeEl.create('payment');
    paymentEl.mount('#payment-element');
    section.style.display = 'block';
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });

    document.getElementById('pay-btn').onclick = handlePayment;
}

async function handlePayment() {
    const btn   = document.getElementById('pay-btn');
    const errEl = document.getElementById('payment-error');
    btn.disabled    = true;
    btn.textContent = 'Processing…';
    errEl.textContent = '';

    const base = document.querySelector('meta[name="base-url"]')?.content ?? '';

    const { error, paymentIntent } = await _stripe.confirmPayment({
        elements: _stripeEl,
        confirmParams: { return_url: location.href },
        redirect: 'if_required',
    });

    if (error) {
        errEl.textContent = error.message;
        btn.disabled    = false;
        btn.textContent = `Pay $${APP_FEE} & Submit`;
        return;
    }

    if (paymentIntent?.status === 'succeeded') {
        // Notify server (best-effort; webhook is the reliable backup)
        try {
            await fetch(base + '/api/confirm_payment.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({
                    payment_intent_id: paymentIntent.id,
                    application_id:    _pendingId,
                    type:              _pendingType,
                }),
            });
        } catch {}

        document.getElementById('payment-section').style.display = 'none';
        document.getElementById('form-success').classList.add('show');
    }
}

// ---- Upload overlay ----
let _overlay = null;
let _overlayPct = null;

function _ensureOverlay() {
    if (_overlay) return;
    _overlay = document.createElement('div');
    _overlay.className = 'upload-overlay';
    _overlay.innerHTML =
        '<div class="upload-spinner"></div>' +
        '<div class="upload-overlay-title">Uploading…</div>' +
        '<div class="upload-overlay-pct"></div>';
    document.body.appendChild(_overlay);
    _overlayPct = _overlay.querySelector('.upload-overlay-pct');
}

function _showOverlay(text) {
    _ensureOverlay();
    if (_overlayPct) _overlayPct.textContent = text || '';
    _overlay.classList.add('visible');
}

function _updateOverlay(text) {
    if (_overlayPct) _overlayPct.textContent = text;
}

function _hideOverlay() {
    if (_overlay) _overlay.classList.remove('visible');
}

function toggleForm() {
    const form = document.getElementById('application-form');
    const btn  = document.getElementById('toggle-form-btn');
    const open = form.classList.toggle('open');
    btn.classList.toggle('active', open);
    if (open) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function showFilename(input, targetId) {
    const el = document.getElementById(targetId);
    if (el && input.files.length > 0)
        el.textContent = Array.from(input.files).map(f => f.name).join(', ');
}

function showPreview(input, targetId) {
    const el = document.getElementById(targetId);
    if (!el) return;
    el.innerHTML = '';
    Array.from(input.files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const url = URL.createObjectURL(file);
        const img = document.createElement('img');
        img.className = 'upload-preview-thumb';
        img.src = url;
        img.onload = () => URL.revokeObjectURL(url);
        el.appendChild(img);
    });
}

// --- Chunked upload ---

async function uploadChunked(file, subdir) {
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE) || 1;
    const uploadId    = Math.random().toString(36).slice(2) + Date.now().toString(36);
    const base        = document.querySelector('meta[name="base-url"]')?.content ?? '';

    for (let i = 0; i < totalChunks; i++) {
        const chunk = file.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE);
        const fd    = new FormData();
        fd.append('chunk',        chunk);
        fd.append('chunk_index',  i);
        fd.append('total_chunks', totalChunks);
        fd.append('upload_id',    uploadId);
        fd.append('subdir',       subdir);
        fd.append('filename',     file.name);

        const res  = await fetch(base + '/api/upload_chunk.php', { method: 'POST', body: fd });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Upload failed');
        if (json.done) return json.path;
    }
}

function setProgress(progressEl, pct, label) {
    if (!progressEl) return;
    if (pct === 100) {
        progressEl.innerHTML = `<div class="upload-status upload-status-ok">✓ ${label}</div>`;
    } else if (pct === 0) {
        progressEl.innerHTML = `<div class="upload-status upload-status-err">${label}</div>`;
    } else {
        progressEl.innerHTML = `<div class="chunk-bar"><div class="chunk-fill" style="width:${pct}%"></div></div><span>${label}</span>`;
    }
}

// Called by each file input's onchange
async function handleFileUpload(input) {
    const subdir      = input.dataset.subdir;
    const pathTarget  = input.dataset.pathTarget;   // hidden input name
    const progressId  = input.dataset.progressId;
    const progressEl  = progressId ? document.getElementById(progressId) : null;
    const filenameEl  = input.dataset.filenameId ? document.getElementById(input.dataset.filenameId) : null;
    const previewId   = input.dataset.previewId;

    if (!input.files.length) return;

    const files = Array.from(input.files);
    if (filenameEl) filenameEl.textContent = files.map(f => f.name).join(', ');
    if (previewId) showPreview(input, previewId);

    // Remove any previously uploaded hidden path inputs for this field
    const form = input.closest('form');
    form.querySelectorAll(`input[name="${pathTarget}"]`).forEach(el => el.remove());

    const base = document.querySelector('meta[name="base-url"]')?.content ?? '';
    let allOk = true;

    _showOverlay(files.length > 1 ? `0 / ${files.length} files` : '');

    for (let fi = 0; fi < files.length; fi++) {
        const file        = files[fi];
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE) || 1;
        const uploadId    = Math.random().toString(36).slice(2) + Date.now().toString(36);
        let   serverPath  = null;

        try {
            for (let i = 0; i < totalChunks; i++) {
                const chunk = file.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE);
                const fd    = new FormData();
                fd.append('chunk',        chunk);
                fd.append('chunk_index',  i);
                fd.append('total_chunks', totalChunks);
                fd.append('upload_id',    uploadId);
                fd.append('subdir',       subdir);
                fd.append('filename',     file.name);

                const filePct   = Math.round(((i + 1) / totalChunks) * 100);
                const barLabel  = files.length > 1 ? `File ${fi + 1}/${files.length} — ${filePct}%` : `${filePct}%`;
                setProgress(progressEl, filePct, barLabel);
                _updateOverlay(files.length > 1 ? `File ${fi + 1} of ${files.length} — ${filePct}%` : `${filePct}%`);

                const res  = await fetch(base + '/api/upload_chunk.php', { method: 'POST', body: fd });
                if (!res.ok) throw new Error(`Server error ${res.status}`);
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Upload failed');
                if (json.done) serverPath = json.path;
            }

            if (serverPath) {
                const hidden = document.createElement('input');
                hidden.type  = 'hidden';
                hidden.name  = pathTarget;
                hidden.value = serverPath;
                input.insertAdjacentElement('afterend', hidden);
            }
        } catch (err) {
            allOk = false;
            setProgress(progressEl, 0, '✗ ' + err.message);
            console.error('Upload error:', err);
        }
    }

    _hideOverlay();

    if (allOk) {
        const label = files.length > 1 ? `${files.length} file(s) uploaded` : files[0].name;
        setProgress(progressEl, 100, label);
    }
}

// --- Validation ---

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

function validatePhone(phone) {
    return /^[\+]?[\d\s\-\(\)]{7,20}$/.test(phone.trim());
}

function validateForm(form) {
    const email = form.querySelector('[name="email"]')?.value ?? '';
    const phone = form.querySelector('[name="phone"]')?.value ?? '';
    if (!validateEmail(email)) {
        alert('Please enter a valid email address.');
        return false;
    }
    if (!validatePhone(phone)) {
        alert('Please enter a valid phone number.');
        return false;
    }

    const links = ['video_link_1', 'video_link_2', 'video_link_3']
        .map(n => form.querySelector(`[name="${n}"]`)?.value.trim().toLowerCase())
        .filter(Boolean);
    if (links.length !== new Set(links).size) {
        alert('Please provide three different video links.');
        return false;
    }
    return true;
}

// Check that required files were actually uploaded (hidden path inputs must exist)
function validateFiles(form, type) {
    const has = name =>
        [...form.querySelectorAll('input[type="hidden"]')].some(el => el.name === name);

    if (type === 'dancer') {
        if (!has('headshot_path')) {
            alert('Please upload your headshot photo (Step 1).');
            return false;
        }
        if (!has('dance_photos_paths[]')) {
            alert('Please upload at least one dance photo (Step 2).');
            return false;
        }
        if (!has('resume_path')) {
            alert('Please upload your résumé (Step 3).');
            return false;
        }
    } else {
        if (!has('resume_path')) {
            alert('Please upload your résumé (Step 1).');
            return false;
        }
        if (!has('cover_letter_path')) {
            alert('Please upload your cover letter (Step 2).');
            return false;
        }
    }
    return true;
}

// --- Form submission ---

async function submitDancerForm(e) {
    e.preventDefault();
    const form = document.getElementById('dancer-form');
    const btn  = form.querySelector('button[type="submit"]');
    if (!validateForm(form)) return;
    if (!validateFiles(form, 'dancer')) return;

    btn.disabled = true;
    btn.textContent = 'Sending…';

    // Build FormData without file inputs (already uploaded via chunks)
    const data = new FormData(form);
    form.querySelectorAll('input[type="file"]').forEach(f => data.delete(f.name));

    try {
        const base = document.querySelector('meta[name="base-url"]')?.content ?? '';
        const res  = await fetch(base + '/api/submit_dancer.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.success && json.client_secret) {
            form.style.display = 'none';
            showPaymentSection(json.client_secret, json.application_id, 'dancer');
        } else if (json.success) {
            // Secret key not yet configured — show success directly
            form.style.display = 'none';
            document.getElementById('form-success').classList.add('show');
        } else {
            alert(json.message || 'Something went wrong. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Send Application';
        }
    } catch {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Send Application';
    }
}

async function submitChoreoForm(e) {
    e.preventDefault();
    const form = document.getElementById('choreo-form');
    const btn  = form.querySelector('button[type="submit"]');
    if (!validateForm(form)) return;
    if (!validateFiles(form, 'choreo')) return;

    btn.disabled = true;
    btn.textContent = 'Sending…';

    const data = new FormData(form);
    form.querySelectorAll('input[type="file"]').forEach(f => data.delete(f.name));

    try {
        const base = document.querySelector('meta[name="base-url"]')?.content ?? '';
        const res  = await fetch(base + '/api/submit_choreographer.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.success && json.client_secret) {
            form.style.display = 'none';
            showPaymentSection(json.client_secret, json.application_id, 'choreo');
        } else if (json.success) {
            form.style.display = 'none';
            document.getElementById('form-success').classList.add('show');
        } else {
            alert(json.message || 'Something went wrong. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Send Application';
        }
    } catch {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Send Application';
    }
}
