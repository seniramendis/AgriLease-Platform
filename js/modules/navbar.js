// ========================================
// AGRILEASE - NAVBAR MODULE
// ========================================

'use strict';

const NavbarModule = (() => {

    function init() {
        initMobileMenu();
        markActiveLink();
        initScrollBehavior();
    }

    function initMobileMenu() {
        const hamburger = document.getElementById('nav-hamburger');
        const drawer = document.getElementById('nav-drawer');
        const overlay = document.getElementById('nav-overlay');
        const closeBtn = document.getElementById('nav-drawer-close');

        if (!hamburger || !drawer) return;

        const open  = () => { drawer.classList.add('open');  overlay.classList.add('show');  document.body.style.overflow = 'hidden'; };
        const close = () => { drawer.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow = ''; };

        hamburger.addEventListener('click', open);
        closeBtn?.addEventListener('click', close);
        overlay?.addEventListener('click', close);

        // Close on Escape
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    }

    function markActiveLink() {
        const path = window.location.pathname.split('/').pop() || 'index.html';
        document.querySelectorAll('.nav-links a, .nav-drawer-link').forEach(link => {
            const href = (link.getAttribute('href') || '').split('/').pop();
            if (href && href !== '#' && path.includes(href)) {
                link.classList.add('active');
                link.style.color = 'var(--primary)';
            }
        });
    }

    function initScrollBehavior() {
        const nav = document.querySelector('nav#main-nav');
        if (!nav) return;
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const current = window.scrollY;
            // Add shadow on scroll
            nav.style.boxShadow = current > 30 ? '0 4px 20px rgba(0,0,0,0.08)' : 'none';
            lastScroll = current;
        }, { passive: true });
    }

    function setUserState(user) {
        const loginLink = document.getElementById('nav-login-link');
        if (!loginLink || !user) return;
        loginLink.textContent = user.name.split(' ')[0];
        loginLink.href = '#profile';
    }

    return { init, setUserState };

})();

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    // Wait for navbar component to load
    setTimeout(NavbarModule.init, 200);
});

window.AgriLease = window.AgriLease || {};
window.AgriLease.NavbarModule = NavbarModule;
