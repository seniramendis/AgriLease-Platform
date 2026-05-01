// ========================================
// AGRILEASE - MAP MODULE
// Uses Leaflet.js for interactive maps
// ========================================

'use strict';

const MapModule = (() => {

    let map = null;
    let markers = [];
    let markerLayer = null;

    // Mock location data for machines
    const machineLocations = [
        { id: 1, name: 'KUBOTA M7040 Tractor',      lat: 7.8731,  lng: 80.7718, category: 'Tractor',   price: 12000, available: true,  district: 'Kurunegala' },
        { id: 2, name: 'YANMAR RG8 Harvester',       lat: 7.9397,  lng: 81.0034, category: 'Harvester', price: 28000, available: true,  district: 'Polonnaruwa' },
        { id: 3, name: 'DJI AGRAS T40 Drone',        lat: 6.9271,  lng: 79.8612, category: 'Drone',     price: 8500,  available: false, district: 'Colombo' },
        { id: 4, name: 'JOHN DEERE 5075E',            lat: 8.3114,  lng: 80.4037, category: 'Tractor',   price: 15000, available: true,  district: 'Anuradhapura' },
        { id: 5, name: 'CLAAS DOMINATOR Harvester',  lat: 7.2833,  lng: 81.6667, category: 'Harvester', price: 32000, available: true,  district: 'Ampara' },
        { id: 6, name: 'MAHINDRA 575 DI Tractor',    lat: 6.0535,  lng: 80.2210, category: 'Tractor',   price: 9000,  available: true,  district: 'Galle' },
        { id: 7, name: 'AGCO GLEANER Harvester',     lat: 9.6615,  lng: 80.0255, category: 'Harvester', price: 25000, available: true,  district: 'Jaffna' },
        { id: 8, name: 'ISEKI TH5365 Mini Tractor',  lat: 7.2906,  lng: 80.6337, category: 'Tractor',   price: 7500,  available: true,  district: 'Kandy' },
        { id: 9, name: 'DJI AGRAS MG-1P Drone',      lat: 8.5874,  lng: 81.2152, category: 'Drone',     price: 6000,  available: true,  district: 'Trincomalee' },
        { id: 10, name: 'SONALIKA 750',               lat: 6.8846,  lng: 79.9585, category: 'Tractor',   price: 11000, available: false, district: 'Gampaha' },
    ];

    // Custom marker icons by category
    function getMarkerIcon(category, available) {
        const color = available ? '#2d6c50' : '#94a3b8';
        const icons = { Tractor: '🚜', Harvester: '🌾', Drone: '🛸', default: '⚙️' };
        const emoji = icons[category] || icons.default;
        return L.divIcon({
            className: '',
            html: `<div style="
                width: 46px; height: 46px;
                background: ${color};
                border-radius: 50% 50% 50% 4px;
                transform: rotate(-45deg);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.25);
                border: 3px solid white;
                cursor: pointer;
            ">
                <span style="transform: rotate(45deg); font-size: 1.2rem;">${emoji}</span>
            </div>`,
            iconSize: [46, 46],
            iconAnchor: [23, 46],
            popupAnchor: [0, -50],
        });
    }

    function createPopupHTML(machine) {
        const statusBadge = machine.available
            ? `<span style="background:#dcfce7; color:#166534; padding:3px 10px; border-radius:99px; font-size:0.75rem; font-weight:700;">✓ Available</span>`
            : `<span style="background:#fee2e2; color:#991b1b; padding:3px 10px; border-radius:99px; font-size:0.75rem; font-weight:700;">✗ Unavailable</span>`;
        return `
            <div style="font-family: 'Manrope', sans-serif; min-width: 220px; padding: 4px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:8px;">
                    <strong style="font-size:1rem; line-height:1.3; color:#0f172a;">${machine.name}</strong>
                    ${statusBadge}
                </div>
                <div style="color:#475569; font-size:0.85rem; margin-bottom:6px;">
                    📍 ${machine.district} &nbsp;|&nbsp; ${machine.category}
                </div>
                <div style="font-size:1.25rem; font-weight:800; color:#2d6c50; margin-bottom:12px;">
                    LKR ${machine.price.toLocaleString()} <span style="font-size:0.8rem; font-weight:600; color:#94a3b8;">/ day</span>
                </div>
                <button onclick="window.location.href='farms.html?id=${machine.id}'"
                    style="width:100%; background:#2d6c50; color:white; border:none; border-radius:10px; padding:10px; font-family:inherit; font-size:0.9rem; font-weight:700; cursor:pointer;">
                    View Details →
                </button>
            </div>`;
    }

    function initMap(containerId = 'map-container') {
        if (map) return;
        map = L.map(containerId, {
            center: [7.8731, 80.7718], // Sri Lanka center
            zoom: 7,
            zoomControl: false,
        });

        // Tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 18,
        }).addTo(map);

        // Custom zoom control
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Marker cluster group
        markerLayer = L.featureGroup().addTo(map);

        addAllMarkers();
    }

    function addAllMarkers(filter = {}) {
        markerLayer.clearLayers();
        markers = [];

        let data = [...machineLocations];
        if (filter.category) data = data.filter(m => m.category === filter.category);
        if (filter.available !== undefined) data = data.filter(m => m.available === filter.available);

        data.forEach(machine => {
            const marker = L.marker([machine.lat, machine.lng], {
                icon: getMarkerIcon(machine.category, machine.available)
            }).bindPopup(createPopupHTML(machine), {
                maxWidth: 280,
                className: 'agrilease-popup'
            });
            markerLayer.addLayer(marker);
            markers.push({ machine, marker });
        });
    }

    function filterMarkers(filters) {
        addAllMarkers(filters);
    }

    function flyToDistrict(lat, lng, zoom = 10) {
        if (map) map.flyTo([lat, lng], zoom, { duration: 1.2 });
    }

    function getUserLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) { reject(new Error('Geolocation not supported')); return; }
            navigator.geolocation.getCurrentPosition(
                pos => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
                err => reject(err)
            );
        });
    }

    function getMapStats() {
        const total = machineLocations.length;
        const available = machineLocations.filter(m => m.available).length;
        const categories = [...new Set(machineLocations.map(m => m.category))];
        return { total, available, unavailable: total - available, categories };
    }

    return { initMap, filterMarkers, flyToDistrict, getUserLocation, getMapStats, machineLocations };

})();

window.AgriLease = window.AgriLease || {};
window.AgriLease.MapModule = MapModule;
