# ✈️ VromonVibe - Domestic Flight Booking System

VromonVibe is a feature-rich, fully responsive, dynamic **Core PHP** web application engineered for domestic flight searching, booking, and administrative management. Powered by a relational MySQL backend, the platform delivers a premium, highly interactive user interface combined with extreme administrative data control.

---

## 🚀 Key Deployed Features

### 1. 🔒 Advanced Authentication & Anti-Friction Security
* **Password Visibility Toggle:** Integrated a clean, interactive eye-icon (`👁️`) on the Login, Registration, and Password Reset forms for instant input visibility control.
* **Direct Instant Password Reset:** Deployed `forgot_password.php` bypassing tedious email/OTP validation. Users can instantly overwrite passwords using secure, standard industry `Bcrypt` cryptographic hashing algorithms.
* **Forced HTTPS Redirection:** Built pre-configured server configuration files (`.htaccess`) using a custom rewrite engine to natively force zero-latency secure SSL connections.

### 2. 💸 Intelligent Dynamic Pricing Engine
* **Dynamic Cabin Class Adjuster:** Dropped static, flat upgrades for a custom-built **40% business class fare multiplier**. This forces realistic premium pricing tiers synchronized precisely with route lengths (e.g., dynamic variations on Dhaka ➔ Saidpur base rates vs. alternative routes).
* **Precise Transaction Ledger:** Seamlessly pushes selected cabin tiers along with exact processed price sums directly to the relational database clusters, completely avoiding calculation drift between subsystems.

### 3. 📊 High-Definition Synchronized Passenger Ledger
* **Live Dashboard Ledger:** Renders custom account ledgers showing passengers their exact booking metrics, tracking real-time fare history and live Cabin Class badges (Economy vs Premium Business Class).
* **PNR Engine & Digital E-Tickets:** Auto-generates unique string alpha-numeric PNR tickets (`VV-XXXX`) upon database confirmation, backed by a native client-side **Print E-Ticket to PDF** feature for instant boarding passes.

### 4. 👑 Extreme Superuser Admin Dashboard (Full CRUD Control)
* **Dynamic Flight Plan CRUD:** System operators can actively inject new active flight metadata pipelines (Airlines, Identification Codes, Timelines, Block Duration, Base Prices) or instantly purge expired tracks.
* **Unified Passenger Revocation Roster:** Engineered using complex SQL `LEFT JOIN` relational mapping arrays to serve admins real-time customer ledgers mapped alongside real route targets with an instant one-click delete command.
* **Centralized User Directory:** Features a protected master grid detailing all registered system accounts, grouping full identities, live emails, and operational role configurations.

### 5. ✨ Fluid UI Design & Staggered Motion Graphics
* **Staggered Results Entrance:** Injected fluid CSS `@keyframes slideUpFade` loops combined with inline timing offsets, forcing flight entries to load smoothly sequentially rather than dropping abruptly.
* **Pixel-Perfect Grid Presentation:** Recoded container flex and grid rules to keep layout metrics completely uniform, making sure columns, pricing items, and airline profiles map flawlessly straight.
* **Active Moving Tracker Animation:** Includes a high-performance linear CSS loop dragging an active aircraft element (`✈`) down runtime paths to enrich visual feedback.

---

## 🛠️ Complete Technology Stack
* **Frontend Execution:** Modern Semantic HTML5, CSS3 Architecture (Flexbox layout arrays, Grid systems, Custom motion loops), Vanilla JavaScript (ES6 Document APIs).
* **Backend Core Engine:** Core PHP (Modular state session handling, Secure object mapping via PHP Data Objects - PDO).
* **Relational Database:** MySQL Data Clusters (Optimized structured schemas and indexing pipelines).
* **Server Middleware Engine:** Custom Apache `.htaccess` configurations for forced HTTPS environment overrides.

---

## 🗄️ Database Setup & Required Schema Upgrades
To ensure perfect deployment compatibility with the newly deployed dashboard ledgers and core transaction models, apply the following SQL structure inside your relational tables:

```sql
ALTER TABLE bookings ADD COLUMN seat_class VARCHAR(50) NOT NULL DEFAULT 'Economy Class';
ALTER TABLE bookings ADD COLUMN total_price INT(11) NOT NULL DEFAULT 0;
