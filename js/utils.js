// ========================================
// AGRILEASE - UTILITY FUNCTIONS
// ========================================

'use strict';

// --- COMPONENT LOADER ---
async function loadComponent(placeholderId, filePath) {
    try {
        const el = document.getElementById(placeholderId);
        if (!el) return;
        const res = await fetch(filePath);
        if (!res.ok) throw new Error(`Failed to load: ${filePath}`);
        el.innerHTML = await res.text();
        // Re-run inline scripts in loaded component
        el.querySelectorAll('script').forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(a => newScript.setAttribute(a.name, a.value));
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    } catch (e) {
        console.warn(`[AgriLease] Component load warning: ${e.message}`);
    }
}

// --- TOAST NOTIFICATIONS ---
const Toast = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toast-container';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'default', duration = 3500) {
        this.init();
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = { success: 'check_circle', error: 'error', warning: 'warning', default: 'info' };
        toast.innerHTML = `
            <span class="material-symbols-outlined" style="font-size: 1.2rem; flex-shrink: 0; font-variation-settings: 'FILL' 1;">${icons[type] || icons.default}</span>
            <span>${message}</span>
        `;
        this.container.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    success: (msg) => Toast.show(msg, 'success'),
    error:   (msg) => Toast.show(msg, 'error'),
    warning: (msg) => Toast.show(msg, 'warning'),
};

// --- MODAL HELPER ---
const Modal = {
    open(id) {
        const overlay = document.getElementById(id);
        if (overlay) overlay.classList.add('active');
    },
    close(id) {
        const overlay = document.getElementById(id);
        if (overlay) overlay.classList.remove('active');
    },
    closeAll() {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
    }
};

// Close modal on overlay click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) Modal.closeAll();
});

// --- FORMATTING UTILITIES ---
const Format = {
    currency(amount, currency = 'LKR') {
        return `${currency} ${Number(amount).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    },

    date(dateStr, options = {}) {
        const defaults = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateStr).toLocaleDateString('en-LK', { ...defaults, ...options });
    },

    relativeTime(dateStr) {
        const diff = (Date.now() - new Date(dateStr)) / 1000;
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
        return Format.date(dateStr);
    },

    initials(name = '') {
        return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    },

    truncate(str, length = 60) {
        return str.length > length ? str.slice(0, length) + '…' : str;
    }
};

// --- STORAGE HELPER ---
const Store = {
    get(key, fallback = null) {
        try {
            const raw = localStorage.getItem(`agrilease_${key}`);
            return raw ? JSON.parse(raw) : fallback;
        } catch { return fallback; }
    },
    set(key, value) {
        try { localStorage.setItem(`agrilease_${key}`, JSON.stringify(value)); } catch {}
    },
    remove(key) {
        localStorage.removeItem(`agrilease_${key}`);
    }
};

// --- DEBOUNCE ---
function debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// --- DOM HELPERS ---
const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];

function createElement(tag, attrs = {}, children = []) {
    const el = document.createElement(tag);
    Object.entries(attrs).forEach(([k, v]) => {
        if (k === 'class') el.className = v;
        else if (k === 'html') el.innerHTML = v;
        else if (k === 'text') el.textContent = v;
        else el.setAttribute(k, v);
    });
    children.forEach(child => el.appendChild(
        typeof child === 'string' ? document.createTextNode(child) : child
    ));
    return el;
}

// --- FORM HELPERS ---
function serializeForm(form) {
    const data = {};
    new FormData(form).forEach((v, k) => { data[k] = v; });
    return data;
}

function validateForm(form, rules = {}) {
    let valid = true;
    Object.entries(rules).forEach(([name, rule]) => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) return;
        const errorEl = form.querySelector(`[data-error="${name}"]`);
        let error = '';

        if (rule.required && !field.value.trim()) error = rule.required === true ? 'This field is required.' : rule.required;
        else if (rule.minLength && field.value.length < rule.minLength) error = `Minimum ${rule.minLength} characters required.`;
        else if (rule.pattern && !rule.pattern.test(field.value)) error = rule.patternMsg || 'Invalid format.';

        if (error) {
            valid = false;
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            if (errorEl) { errorEl.textContent = error; errorEl.style.display = 'block'; }
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (errorEl) errorEl.style.display = 'none';
        }
    });
    return valid;
}

// --- LOADING STATE ---
function setLoading(button, loading = true) {
    if (!button) return;
    if (loading) {
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = `<span class="material-symbols-outlined" style="font-size:1rem; animation: spin 1s linear infinite;">autorenew</span> Loading...`;
        button.disabled = true;
    } else {
        button.innerHTML = button.dataset.originalText || button.innerHTML;
        button.disabled = false;
    }
}

// Spin animation
const spinStyle = document.createElement('style');
spinStyle.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(spinStyle);

// --- PAGINATION HELPER ---
class Paginator {
    constructor({ data, perPage = 10, renderFn, containerId, paginationId }) {
        this.data = data;
        this.perPage = perPage;
        this.renderFn = renderFn;
        this.container = document.getElementById(containerId);
        this.pagination = document.getElementById(paginationId);
        this.currentPage = 1;
        this.totalPages = Math.ceil(data.length / perPage);
    }

    render() {
        const start = (this.currentPage - 1) * this.perPage;
        const slice = this.data.slice(start, start + this.perPage);
        if (this.container) this.container.innerHTML = slice.map(this.renderFn).join('');
        this.renderPagination();
    }

    renderPagination() {
        if (!this.pagination || this.totalPages <= 1) return;
        let html = '';
        for (let i = 1; i <= this.totalPages; i++) {
            html += `<button class="filter-pill ${i === this.currentPage ? 'active' : ''}" onclick="this._paginator.goTo(${i})">${i}</button>`;
        }
        this.pagination.innerHTML = html;
    }

    goTo(page) {
        this.currentPage = Math.max(1, Math.min(page, this.totalPages));
        this.render();
    }

    setData(data) {
        this.data = data;
        this.totalPages = Math.ceil(data.length / this.perPage);
        this.currentPage = 1;
        this.render();
    }
}

// --- EXPORT ---
window.AgriLease = window.AgriLease || {};
Object.assign(window.AgriLease, { Toast, Modal, Format, Store, debounce, $, $$, createElement, serializeForm, validateForm, setLoading, Paginator, loadComponent });
