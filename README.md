# Monitoring Management System

A capstone project developed for managing agricultural supplies.  
This system allows users to track supplies, manage stock levels, and generate reports.  
Built with **Laravel**, **MySQL/MariaDB**, and **Bootstrap**.

---

## Features
- User authentication (Admin & User roles)
- Supply inventory management (add, update, delete)
- 
- 
- 
- 

---

## Technologies Used
- PHP / Laravel
- MySQL / MariaDB
- Blade Templates
- Bootstrap / Tailwind CSS
- GitHub for version control

---

## Installation Guide

### 1. Clone the Repository
```bash
git clone https://github.com/ralph-nig/ATI-RTC1.git
cd ATI-RTC1
```

### 2. Install Dependencies
```bash
composer install
npm
```

### 3. Environment Setup
```bash
cp .env.example .env
```

### 4. Configure database in .env:
DB_DATABASE=agrisupply
DB_USERNAME=root
DB_PASSWORD=

### 5. Generate App Key
```bash
php artisan key: generate
```

### 6. Run Migrations
```bash
php artisan migrate
```

#### 7. Start Development Server
```bash
php artisan serve
```

Visit agrisupply.test

## User Roles
Admin: Manage users, supplies, and help requests
User: View and Update supply records

## Project Structure
app/            # Application logic (Models, Controllers)
database/       # Migrations and seeders
public/         # Public assets (css, js, images)
resources/      # Blade templates
routes/         # Web and API routes

## Contributors
Leader: Christian Mathew Catubig
Members: Diane Cruz
         Mark Gerald Bruan
         Ralph Bolinas

## License 
This project is for academic purposes only

