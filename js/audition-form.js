function toggleForm() {
    const form = document.getElementById('application-form');
    const btn  = document.getElementById('toggle-form-btn');
    const open = form.classList.toggle('open');
    btn.classList.toggle('active', open);
    if (open) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function showFilename(input, targetId) {
    const el = document.getElementById(targetId);
    if (!el) return;
    if (input.files && input.files.length > 0) {
        const names = Array.from(input.files).map(f => f.name).join(', ');
        el.textContent = names;
    }
}

async function submitDancerForm(e) {
    e.preventDefault();
    const form = document.getElementById('dancer-form');
    const btn  = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Sending…';

    const data = new FormData(form);
    try {
        const base = document.querySelector('meta[name="base-url"]')?.content ?? '';
        const res  = await fetch(base + '/api/submit_dancer.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.success) {
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
    btn.disabled = true;
    btn.textContent = 'Sending…';

    const data = new FormData(form);
    try {
        const base = document.querySelector('meta[name="base-url"]')?.content ?? '';
        const res  = await fetch(base + '/api/submit_choreographer.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.success) {
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
