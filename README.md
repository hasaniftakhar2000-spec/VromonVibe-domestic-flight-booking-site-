# VromonVibe ✈️ - Domestic Flight Booking System

A full-stack mini web application built for booking domestic flights in Bangladesh. Users can search for flights, book tickets, generate PDF e-tickets, and manage their bookings from a personalized dashboard. Admins have access to a secure roster to manage and delete flight tickets.

## 🔗 Live Demo
**Website:** [https://vromonvibe-bd.infinityfreeapp.com](https://vromonvibe-bd.infinityfreeapp.com)

## ✨ Key Features
* **User Authentication:** Secure login and registration system.
* **Flight Search:** Search available domestic flights based on routes and dates.
* **Ticket Booking & PDF:** Dynamic price calculation (Economy/Business) and printable E-Ticket generation.
* **User Dashboard:** Users can view their booked tickets and PNR status.
* **Admin Panel:** Admins can view all bookings and delete/cancel tickets (CRUD operations).
* **Security:** PDO Prepared Statements to prevent SQL injection and secure session management.

## 🛠️ Technology Stack
* **Frontend:** HTML5, CSS3, JavaScript
* **Backend:** PHP (Core)
* **Database:** MySQL
* **Hosting:** InfinityFree (Server) & GitHub (Version Control)

## 🚀 Setup Instructions (Local Deployment)
1. Download and install [XAMPP](https://www.apachefriends.org/).
2. Clone this repository into the `htdocs` folder (`C:\xampp\htdocs\vromonvibe`).
3. Start **Apache** and **MySQL** from the XAMPP Control Panel.
4. Open phpMyAdmin (`http://localhost/phpmyadmin`) and create a database named `vromonvibe_db`.
5. Import the `database.sql` file (if provided) or manually create the `users`, `flights`, and `bookings` tables.
6. Update the `db.php` file with your local database credentials (`root` and empty password).
7. Open your browser and navigate to: `http://localhost/vromonvibe`
