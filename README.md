<h1>AgriLease - Agricultural Machinery Sharing Platform 🚜</h1>

<p>AgriLease is a web-based "sharing economy" platform designed to bridge the agricultural mechanization gap in Sri Lanka[cite: 571, 572]. [cite_start]By connecting machine owners (Lessors) with smallholder farmers (Lessees) in regions like Anuradhapura, Polonnaruwa, and Ampara, the platform optimizes asset utilization and provides affordable access to modern agricultural technology.</p>

This project digitizes the local "Custom Hiring" system, addressing unique logistical challenges, seasonal "Maha" and "Yala" pressures, and financial hurdles for Sri Lankan farmers.

---

<h2>🛠 Technology Stack</h2>

This project utilizes a modern, zero-cost bootstrap "Agri-Tech" stack optimized for 2026:

### Backend & Database
* **Framework:** Laravel 10.x (PHP 8.2) [cite: 718, 744]
* **Database:** PostgreSQL 15 [cite: 718, 744]
* **Spatial Data:** PostGIS (for mapping irregular paddy fields and routing) [cite: 633, 634, 635, 744]
* **Authentication:** Supabase Auth [cite: 641, 644, 744]

### Frontend & Mapping
* [cite_start]**UI/UX:** HTML, CSS, JavaScript [cite: 744]
* [cite_start]**Mapping:** Leaflet.js (using OpenStreetMap tile layers) [cite: 718, 744]

### Third-Party Integrations
* [cite_start]**Payments:** PayHere Sandbox / LankaPay (LKR-native transactions) [cite: 718, 744]
* [cite_start]**Messaging:** Notify.lk / WhatsApp Business API [cite: 718, 744]
* [cite_start]**Identity Verification:** Tesseract.js (Client-side OCR for NIC validation) [cite: 656, 657]

---

## 👥 The Team (CMU / HDCSE)

[cite_start]Developed for the HD in Computing & Software Engineering at Cardiff Metropolitan University[cite: 742].

* [cite_start]**Devin Kulasekere:** Lead Front-end Developer [cite: 745]
* **K. [cite_start]Mabhisha Rashmika:** Lead Back-end Developer [cite: 745]
* **Senira Mendis:** Scrum Master & Backend Developer [cite: 745]
* **J.M.A.V. [cite_start]Lakshan:** UI/UX Designer & System Analyst [cite: 745]
* **K.D.G. [cite_start]Pamod Dhananjana:** QA & Integration Specialist [cite: 745]

---

## 🌿 Git Workflow & Branching Strategy

[cite_start]We follow a strict Git Flow methodology utilizing specific category prefixes for our 6-week development cycle[cite: 3, 8, 12, 15, 20]:

1. **`main`**: Production-ready code only.
2. **`develop`**: The active integration branch. **All new branches must be created from `develop`.**
3. **`staging`**: Pre-production testing environment.

### Feature Branch Naming Convention
When branching off `develop`, use the following prefixes based on your module:
* [cite_start]`frontend/*` (e.g., `frontend/project-setup`) [cite: 8]
* [cite_start]`backend/*` (e.g., `backend/database-schema`) [cite: 15]
* [cite_start]`docs/*` (e.g., `docs/requirements-analysis`) [cite: 23]
* [cite_start]`test/*` or `devops/*` (e.g., `devops/docker-setup`) [cite: 20]

---

## 🚀 Getting Started (Local Development)

*(Note: These instructions assume Docker is installed as per the DevOps setup)*

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/YOUR-ORG/AgriLease-Platform.git](https://github.com/YOUR-ORG/AgriLease-Platform.git)
   cd AgriLease-Platform
