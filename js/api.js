// ========================================
// AGRILEASE - API MODULE
// Handles all data requests (mock + real)
// ========================================

'use strict';

const API = (() => {

    const BASE_URL = '/api/v1'; // Change to actual backend URL
    const MOCK_DELAY = 400;     // Simulate network latency

    // ---- MOCK DATA ----
    const mockMachines = [
        { id: 1, name: 'KUBOTA M7040 Tractor', category: 'Tractor', district: 'Kurunegala', price: 12000, unit: 'day', owner: 'Nimal Perera', rating: 4.8, reviews: 24, available: true, image: 'https://images.unsplash.com/photo-1530267981375-f0de937f5f13?w=400', operator: true },
        { id: 2, name: 'YANMAR RG8 Harvester',  category: 'Harvester', district: 'Polonnaruwa', price: 28000, unit: 'day', owner: 'Sunil Bandara', rating: 4.6, reviews: 18, available: true, image: 'https://images.unsplash.com/photo-1589923158776-cb4dc7589b3b?w=400', operator: true },
        { id: 3, name: 'DJI AGRAS T40 Drone',   category: 'Drone', district: 'Colombo', price: 8500, unit: 'acre', owner: 'TechFarm LK', rating: 4.9, reviews: 41, available: false, image: 'https://images.unsplash.com/photo-1508614589041-895b88991e3e?w=400', operator: false },
        { id: 4, name: 'JOHN DEERE 5075E',       category: 'Tractor', district: 'Anuradhapura', price: 15000, unit: 'day', owner: 'Kamal Silva', rating: 4.7, reviews: 30, available: true, image: 'https://images.unsplash.com/photo-1563514227147-6d2af8c74b09?w=400', operator: true },
        { id: 5, name: 'CLAAS DOMINATOR Harvester', category: 'Harvester', district: 'Ampara', price: 32000, unit: 'day', owner: 'Harvesting Pro LK', rating: 4.5, reviews: 12, available: true, image: 'https://images.unsplash.com/photo-1598738131638-a5e1f3a5b4e0?w=400', operator: true },
        { id: 6, name: 'MAHINDRA 575 DI Tractor', category: 'Tractor', district: 'Galle', price: 9000, unit: 'day', owner: 'Priya Fernando', rating: 4.3, reviews: 9, available: true, image: 'https://images.unsplash.com/photo-1574943320219-553eb213f72d?w=400', operator: false },
    ];

    const mockFarms = [
        { id: 1, name: 'Perera Family Farm', location: 'Kurunegala', size: 12.5, crop: 'Rice', status: 'active', owner: 'Nimal Perera' },
        { id: 2, name: 'Green Valley Estate', location: 'Kandy', size: 45.0, crop: 'Tea', status: 'active', owner: 'Saman Jayasuriya' },
        { id: 3, name: 'Bandara Paddy Fields', location: 'Polonnaruwa', size: 8.2, crop: 'Rice', status: 'idle', owner: 'Sunil Bandara' },
        { id: 4, name: 'Coastal Coconut Grove', location: 'Negombo', size: 22.0, crop: 'Coconut', status: 'active', owner: 'Mary Silva' },
    ];

    const mockCrops = [
        { id: 1, name: 'Rice (Paddy)', season: 'Maha & Yala', duration: '3-4 months', water: 'High', machinery: ['Tractor', 'Harvester'], tips: 'Best planted at the onset of monsoon. Use puddled transplanting for wet season.', icon: 'grass' },
        { id: 2, name: 'Maize', season: 'Year-round', duration: '3 months', water: 'Medium', machinery: ['Tractor', 'Plough'], tips: 'Requires well-drained soil. Apply nitrogen fertilizer in split doses.', icon: 'eco' },
        { id: 3, name: 'Tea', season: 'Year-round', duration: 'Perennial', water: 'Medium-High', machinery: ['Tea Harvester', 'Sprayer'], tips: 'Maintain 18-24°C. Regular pruning every 3-4 years improves yield significantly.', icon: 'local_florist' },
        { id: 4, name: 'Coconut', season: 'Year-round', duration: 'Perennial', water: 'Medium', machinery: ['Sprayer', 'Loader'], tips: 'Plant at 7.5m spacing. Intercropping with pineapple maximizes land use.', icon: 'park' },
        { id: 5, name: 'Vegetable Mix', season: 'Dry Season', duration: '1-3 months', water: 'Low-Medium', machinery: ['Mini Tractor', 'Sprayer'], tips: 'Start with soil testing. Drip irrigation reduces water usage by up to 50%.', icon: 'spa' },
        { id: 6, name: 'Sugarcane', season: 'Year-round', duration: '12-18 months', water: 'High', machinery: ['Tractor', 'Harvester'], tips: 'Ratoon cropping reduces replanting costs. Trash mulching conserves soil moisture.', icon: 'energy_program_saving' },
    ];

    const mockWeatherData = {
        current: { temp: 31, feels_like: 35, humidity: 78, wind: 12, condition: 'Partly Cloudy', icon: 'partly_cloudy_day', location: 'Colombo, Western' },
        forecast: [
            { day: 'Today',   high: 33, low: 25, condition: 'Partly Cloudy', icon: 'partly_cloudy_day', rain: 20 },
            { day: 'Tomorrow',high: 30, low: 24, condition: 'Light Rain',    icon: 'rainy',             rain: 75 },
            { day: 'Wed',     high: 28, low: 23, condition: 'Heavy Rain',    icon: 'thunderstorm',      rain: 90 },
            { day: 'Thu',     high: 32, low: 25, condition: 'Sunny',         icon: 'sunny',             rain: 5  },
            { day: 'Fri',     high: 34, low: 26, condition: 'Sunny',         icon: 'sunny',             rain: 5  },
            { day: 'Sat',     high: 31, low: 24, condition: 'Cloudy',        icon: 'cloud',             rain: 35 },
            { day: 'Sun',     high: 29, low: 23, condition: 'Rainy',         icon: 'rainy',             rain: 65 },
        ]
    };

    const mockStats = {
        totalMachines: 1247,
        totalOperators: 532,
        totalFarms: 3891,
        activeRentals: 89,
        monthlyRevenue: 4850000,
        avgRating: 4.7,
    };

    // ---- PRIVATE HELPER ----
    function mockResponse(data, delay = MOCK_DELAY) {
        return new Promise(resolve => setTimeout(() => resolve({ success: true, data }), delay));
    }

    // ---- PUBLIC API ----
    return {

        // Machines
        getMachines(filters = {}) {
            let results = [...mockMachines];
            if (filters.category) results = results.filter(m => m.category === filters.category);
            if (filters.district) results = results.filter(m => m.district.toLowerCase().includes(filters.district.toLowerCase()));
            if (filters.available !== undefined) results = results.filter(m => m.available === filters.available);
            if (filters.query) results = results.filter(m => m.name.toLowerCase().includes(filters.query.toLowerCase()));
            return mockResponse(results);
        },

        getMachineById(id) {
            const machine = mockMachines.find(m => m.id === Number(id));
            return machine ? mockResponse(machine) : Promise.reject({ success: false, error: 'Machine not found' });
        },

        // Farms
        getFarms(filters = {}) {
            let results = [...mockFarms];
            if (filters.status) results = results.filter(f => f.status === filters.status);
            return mockResponse(results);
        },

        // Crops
        getCrops() {
            return mockResponse(mockCrops);
        },

        // Weather
        getWeather(location = 'Colombo') {
            return mockResponse({ ...mockWeatherData, location });
        },

        // Stats
        getStats() {
            return mockResponse(mockStats);
        },

        // Rentals
        createRental(payload) {
            console.log('[API] Creating rental:', payload);
            return mockResponse({ id: Date.now(), ...payload, status: 'pending' }, 800);
        },

        // Auth (mock)
        login(email, password) {
            if (email && password) {
                return mockResponse({ token: 'mock_token_xyz', user: { name: 'Nimal Perera', email, role: 'farmer' } }, 600);
            }
            return Promise.reject({ success: false, error: 'Invalid credentials' });
        },

        register(data) {
            return mockResponse({ id: Date.now(), ...data, role: 'farmer' }, 800);
        },

        // Generic fetch wrapper for real API calls
        async request(endpoint, options = {}) {
            try {
                const token = localStorage.getItem('agrilease_token');
                const res = await fetch(`${BASE_URL}${endpoint}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        ...(token && { Authorization: `Bearer ${token}` }),
                        ...options.headers,
                    },
                    ...options,
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return await res.json();
            } catch (err) {
                console.error('[API Error]', err);
                throw err;
            }
        }
    };
})();

window.AgriLease = window.AgriLease || {};
window.AgriLease.API = API;
