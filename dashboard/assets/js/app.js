/**
 * TaskFlow - Main Application JS
 */

// Auto-dismiss alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.3s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});

// Close mobile sidebar on link click
document.querySelectorAll('.sidebar .nav-item').forEach(item => {
    item.addEventListener('click', () => {
        document.getElementById('sidebar')?.classList.remove('open');
    });
});

// Close sidebar overlay on mobile
document.addEventListener('click', (e) => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.querySelector('.mobile-toggle');
    if (sidebar && sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) && !toggle?.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // ESC to close modals
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
    }
});

// AJAX helper
function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    };

    if (data && !(data instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    } else if (data) {
        options.body = data;
    }

    return fetch(url, options).then(r => r.json());
}

// Confirm before leaving if form is dirty
let formDirty = false;
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('input', () => { formDirty = true; });
    form.addEventListener('submit', () => { formDirty = false; });
});

// Tooltip-like title on hover (native browser tooltip is fine for now)
console.log('TaskFlow v1.0 loaded');
