# AgriLease - Agricultural Machinery Sharing Platform 🚜

AgriLease is a web-based "sharing economy" platform designed to bridge the agricultural mechanization gap in Sri Lanka. By connecting machine owners (Lessors) with smallholder farmers (Lessees) in regions like Anuradhapura, Polonnaruwa, and Ampara, the platform optimizes asset utilization and provides affordable access to modern agricultural technology.

This project digitizes the local "Custom Hiring" system, addressing unique logistical challenges, seasonal "Maha" and "Yala" pressures, and financial hurdles for Sri Lankan farmers.

---

## 🛠 Technology Stack

This project utilizes a modern, zero-cost bootstrap "Agri-Tech" stack optimized for 2026:

### Backend & Database
* **Framework:** Laravel 10.x (PHP 8.2) 
* **Database:** PostgreSQL 15 
* **Spatial Data:** PostGIS (for mapping irregular paddy fields and routing) 
* **Authentication:** Supabase Auth 

### Frontend & Mapping
* **UI/UX:** HTML, CSS, JavaScript 
* **Mapping:** Leaflet.js (using OpenStreetMap tile layers) 

### Third-Party Integrations
* **Payments:** PayHere Sandbox / LankaPay (LKR-native transactions)
* **Messaging:** Notify.lk / WhatsApp Business API [cite: 718, 744]
* **Identity Verification:** Tesseract.js (Client-side OCR for NIC validation) 

---

## 👥 The Team 

Developed for the HD in Computing & Software Engineering at Cardiff Metropolitan University.

* **Devin Kulasekere:** Lead Front-end Developer 
* **K. Mabhisha Rashmika:** Lead Back-end Developer 
* **Senira Mendis:** Scrum Master & Backend Developer 
* **J.M.A.V. Lakshan:** UI/UX Designer & System Analyst 
* **K.D.G. Pamod Dhananjana:** QA & Integration Specialist 

---

## 🌿 Git Workflow & Branching Strategy

We follow a strict Git Flow methodology utilizing specific category prefixes for our 6-week development cycle:

1. **`main`**: Production-ready code only.
2. **`develop`**: The active integration branch. **All new branches must be created from `develop`.**
3. **`staging`**: Pre-production testing environment.

### Feature Branch Naming Convention
When branching off `develop`, use the following prefixes based on your module:
* `frontend/*` (e.g., `frontend/project-setup`) 
* `backend/*` (e.g., `backend/database-schema`) 
* `docs/*` (e.g., `docs/requirements-analysis`) 
* `test/*` or `devops/*` (e.g., `devops/docker-setup`) 

---

## 🚀 Getting Started (Local Development)

*(Note: These instructions assume Docker is installed as per the DevOps setup)*

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/YOUR-ORG/AgriLease-Platform.git](https://github.com/YOUR-ORG/AgriLease-Platform.git)
   cd AgriLease-Platform
Switch to the develop branch:

Bash
git checkout develop
Create your feature branch:

Bash
git checkout -b your-prefix/your-feature-name
Environment Setup:

Copy .env.example to .env

Configure your local PostgreSQL, Supabase, and PayHere credentials.

Run composer install and npm install.

Run Migrations & Seeders:

Bash
php artisan migrate:fresh --seed
Document Version: 6.0 FINAL CORRECTED | Project: AgriLease Platform 


***

To add this to your repository, you should checkout the `develop` branch, create a f
