// ========================================
// AGRILEASE - APP BOOTSTRAP
// ========================================

'use strict';

const App = (() => {

    // ---- CONFIG ----
    const config = {
        appName: 'AgriLease',
        version: '1.0.0',
        defaultLocale: 'en-LK',
        currency: 'LKR',
        districts: [
            'Colombo','Gampaha','Kalutara','Kandy','Matale','Nuwara Eliya',
            'Galle','Matara','Hambantota','Jaffna','Kilinochchi','Mannar',
            'Vavuniya','Mullaitivu','Batticaloa','Ampara','Trincomalee',
            'Kurunegala','Puttalam','Anuradhapura','Polonnaruwa','Badulla',
            'Monaragala','Ratnapura','Kegalle'
        ],
        machineCategories: ['Tractor', 'Harvester', 'Agricultural Drone', 'Plough', 'Sprayer', 'Loader', 'Mini Tractor'],
    };

    // ---- STATE ----
    const state = {
        user: null,
        isLoggedIn: false,
    };

    // ---- INIT ----
    function init() {
        loadUser();
        initTheme();
        loadComponents();
        console.log(`[${config.appName} v${config.version}] App initialized`);
    }

    function loadUser() {
        const user = AgriLease.Store.get('user');
        const token = AgriLease.Store.get('token');
        if (user && token) {
            state.user = user;
            state.isLoggedIn = true;
        }
    }

    function initTheme() {
        // Scroll-based nav shadow
        const nav = document.querySelector('nav');
        if (nav) {
            window.addEventListener('scroll', () => {
                nav.style.boxShadow = window.scrollY > 20 ? '0 4px 20px rgba(0,0,0,0.08)' : 'none';
            }, { passive: true });
        }

        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.card, .stat-card, .feature-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(el);
        });
    }

    function loadComponents() {
        const navbar = document.getElementById('navbar-placeholder');
        const footer = document.getElementById('footer-placeholder');
        const depth = getPathDepth();

        if (navbar) AgriLease.loadComponent('navbar-placeholder', `${depth}components/navbar.html`);
        if (footer) AgriLease.loadComponent('footer-placeholder', `${depth}components/footer.html`);
    }

    function getPathDepth() {
        const path = window.location.pathname;
        const depth = (path.match(/\//g) || []).length;
        return depth <= 1 ? '' : depth === 2 ? '../' : '../../';
    }

    // ---- AUTH ----
    function login(user, token) {
        state.user = user;
        state.isLoggedIn = true;
        AgriLease.Store.set('user', user);
        AgriLease.Store.set('token', token);
        AgriLease.Toast.success(`Welcome back, ${user.name}!`);
    }

    function logout() {
        state.user = null;
        state.isLoggedIn = false;
        AgriLease.Store.remove('user');
        AgriLease.Store.remove('token');
        AgriLease.Toast.show('You have been logged out.');
        setTimeout(() => window.location.href = '/index.html', 800);
    }

    // ---- MACHINE CARD RENDERER ----
    function renderMachineCard(machine) {
        const statusBadge = machine.available
            ? '<span class="badge badge-success"><span class="material-symbols-outlined" style="font-size:0.75rem; font-variation-settings:\'FILL\' 1;">check_circle</span> Available</span>'
            : '<span class="badge badge-danger"><span class="material-symbols-outlined" style="font-size:0.75rem;">cancel</span> Unavailable</span>';

        return `
        <div class="card machine-card" data-id="${machine.id}" style="cursor: pointer;" onclick="window.location.href='farms.html?id=${machine.id}'">
            <div style="position: relative; overflow: hidden; border-radius: 20px 20px 0 0; height: 200px;">
                <img src="${machine.image}" alt="${machine.name}" style="width:100%; height:100%; object-fit:cover; transition: transform 0.4s ease;" 
                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <div style="position:absolute; top:12px; left:12px;">${statusBadge}</div>
                ${machine.operator ? '<div style="position:absolute; top:12px; right:12px;"><span class="badge badge-info"><span class="material-symbols-outlined" style="font-size:0.75rem; font-variation-settings: \'FILL\' 1;">person_check</span> Operator</span></div>' : ''}
            </div>
            <div class="card-body">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:0.5rem;">
                    <h3 style="font-size:1.1rem; line-height:1.3;">${machine.name}</h3>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="font-size:1.3rem; font-weight:800; color:var(--primary);">LKR ${machine.price.toLocaleString()}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">/ ${machine.unit}</div>
                    </div>
                </div>
                <div style="display:flex; gap:1rem; color:var(--text-muted); font-size:0.85rem; margin-bottom:1rem;">
                    <span style="display:flex; align-items:center; gap:4px;">
                        <span class="material-symbols-outlined" style="font-size:1rem;">location_on</span>${machine.district}
                    </span>
                    <span style="display:flex; align-items:center; gap:4px;">
                        <span class="material-symbols-outlined" style="font-size:1rem;">category</span>${machine.category}
                    </span>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span style="color:#f59e0b; font-size:0.9rem; font-weight:800;">★ ${machine.rating}</span>
                        <span style="color:var(--text-muted); font-size:0.8rem;">(${machine.reviews} reviews)</span>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); bookMachine(${machine.id})">
                        <span class="material-symbols-outlined" style="font-size:1rem;">event_available</span> Book
                    </button>
                </div>
            </div>
        </div>`;
    }

    // ---- FARM CARD RENDERER ----
    function renderFarmCard(farm) {
        const statusColor = farm.status === 'active' ? 'success' : 'warning';
        return `
        <div class="card" style="padding: 1.5rem;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1rem;">
                <div>
                    <h3 style="font-size:1.1rem;">${farm.name}</h3>
                    <p style="color:var(--text-muted); font-size:0.875rem; margin-top:4px;">
                        <span class="material-symbols-outlined" style="font-size:0.875rem; vertical-align:middle;">location_on</span>
                        ${farm.location}
                    </p>
                </div>
                <span class="badge badge-${statusColor}">${farm.status}</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; padding:1rem; background:var(--bg-light); border-radius:var(--radius-md);">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">Size</div>
                    <div style="font-weight:700;">${farm.size} ha</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">Crop</div>
                    <div style="font-weight:700;">${farm.crop}</div>
                </div>
            </div>
            <div style="margin-top:1rem; display:flex; gap:0.75rem;">
                <button class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">View</button>
                <button class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">Find Machinery</button>
            </div>
        </div>`;
    }

    // ---- GLOBAL BOOK MACHINE ----
    window.bookMachine = function(id) {
        if (!state.isLoggedIn) {
            AgriLease.Toast.warning('Please login to book a machine.');
            return;
        }
        AgriLease.Toast.success('Booking initiated! Redirecting...');
        setTimeout(() => window.location.href = `farms.html?id=${id}&action=book`, 800);
    };

    return { init, login, logout, state, config, renderMachineCard, renderFarmCard };

})();

// Auto-init when DOM ready
document.addEventListener('DOMContentLoaded', App.init);

window.AgriLease = window.AgriLease || {};
window.AgriLease.App = App;
