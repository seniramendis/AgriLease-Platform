# AgriLease - Frontend Project Setup

## Overview
This branch (`frontend/project-setup`) contains the initial foundational setup for the AgriLease platform's frontend. It establishes a zero-build-step, pure HTML/CSS/JavaScript architecture designed for high performance, rapid loading, and low bandwidth usage in rural Sri Lankan environments.

## Technologies Used
* **HTML5:** Semantic structure with dynamic component-based injection.
* **CSS3:** Custom framework utilizing CSS variables (`theme.css`) for a consistent agricultural color scheme and a mobile-first responsive design.
* **Vanilla JavaScript (ES6+):** Utilizes the IIFE module pattern (`App`, `API`, `MapModule`, `NavbarModule`) for encapsulated logic without the need for a bundler like Webpack or Vite.
* **Leaflet.js (v1.9):** Open-source mapping library integrated via CDN for the geospatial machinery locator.
* **Material Symbols:** Google's icon font for lightweight vector UI icons.

## Directory Structure
```text
├── assets/
│   ├── css/
│   │   ├── forms.css      # Reusable form elements, inputs, and steppers
│   │   ├── main.css       # Core layout, buttons, cards, and typography
│   │   └── theme.css      # CSS variables (colors, spacing, shadows)
│   └── img/               # Static images and backgrounds
├── components/
│   ├── footer.html        # Reusable footer component
│   └── navbar.html        # Reusable responsive navigation bar with mobile drawer
├── js/
│   ├── modules/
│   │   └── navbar.js      # Navigation UI and scroll behavior logic
│   ├── api.js             # API module with mock data for development
│   ├── app.js             # Main application bootstrap and state management
│   ├── map.js             # Leaflet mapping, custom markers, and location logic
│   └── utils.js           # Utility functions (Toast notifications, Component Loader, Formatters)
├── pages/
│   ├── crops.html         # Crop guide and machinery planner
│   ├── farms.html         # Machine listing grid and filtering interface
│   ├── map.html           # Full-screen geospatial machine locator view
│   ├── reports.html       # Analytics, charts, and reporting dashboard
│   ├── template.html      # Boilerplate layout for creating new pages
│   └── weather.html       # Agricultural weather forecast and farming advisories
└── index.html             # Landing page with hero section and quick search

Setup & Execution
Because this project utilizes a vanilla HTML/JS stack with no build tools, running it locally is incredibly simple.

Clone the repository and switch to the branch:

Bash
git clone [https://github.com/YOUR-ORG/AgriLease-Platform.git](https://github.com/YOUR-ORG/AgriLease-Platform.git)
cd AgriLease-Platform
git checkout frontend/project-setup
Serve the files locally:
The application uses the native JavaScript fetch() API (in utils.js) to load HTML components like the Navbar and Footer. Because of browser security (CORS), you cannot simply double-click index.html. You must serve the folder over a local HTTP server.

Use any of the following methods:

VS Code: Install and click the "Live Server" extension.

Node.js: Run npx serve . in the root folder.

Python: Run python -m http.server 8000 in the root folder.

PHP: Run php -S localhost:8000 in the root folder.

View the Application:
Open your browser and navigate to http://localhost:8000 (or the port provided by your local server).

Features Implemented in this Branch
Dynamic Component Loading: The loadComponent utility dynamically fetches and injects the shared Navbar and Footer across all pages without requiring a backend templating engine.

Mock API Integration: api.js simulates network latency and returns mock JSON data for machines, crops, weather, and statistics, allowing UI development to proceed independently of the backend.

Geospatial Mapping: map.js initializes a Leaflet map with custom emoji-based markers, interactive popup cards, and district-level fly-to animations.

Responsive UI System: A custom CSS system providing a responsive grid, glassmorphism effects on the landing page, toast notifications, and modular data cards.

Workflow Commits Satisfied
This branch completes the initial 15 frontend commits assigned to Devin Kulasekere for Sprint 1 (Week 1):


chore(init): initialize frontend project structure 


chore(deps): setup HTML/CSS/JavaScript base 


chore(leaflet): integrate Leaflet.js v1.9 for mapping 


config(structure): create folders (pages, components, assets, js) 


style(theme): create agricultural color scheme (green/brown/gold) 


feat(pwa): setup Progressive Web App manifest 


feat(pwa): create service worker for offline functionality 


chore(leaflet): configure Leaflet map initialization 


style(responsive): create mobile-first CSS framework 


feat(navigation): create responsive navigation component 


feat(layout): create base page layout template 


style(forms): create reusable form styles 


feat(utils): create JavaScript utility functions (date, format, validation) 


config(api): setup API client for backend communication 


docs(frontend): document frontend architecture
