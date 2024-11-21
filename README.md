# FSTT Student Application Management System

## Overview
A web-based application management system for the Faculty of Sciences and Technologies Tangier (FSTT), handling student applications for Engineering (CI) and Master's (MST) programs.

---

## Features
- Admin authentication system.
- Student application processing for:
  - **Engineering programs**: LSI, GI, GEO, GEMI, GA.
  - **Master's programs**: SE, AISD, ITBD, GC, GE, MMSD.
- Excel file management for applications.
- Secure admin dashboard.
- Rate limiting for login attempts.

---

## Technologies
- **PHP 8.1+**
- **PHPSpreadsheet**
- **PHPMailer**
- **HTML/CSS/JavaScript**
- **AJAX**

---

## Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/fstt-application-system.git
```
# Install dependencies
```bash
composer install
```
# Configure environment
```bash
cp config.example.php config.php
```
Configuration
Update config.php with your settings:

```bash
define('ADMIN_USERNAME', 'your_username');
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_user');
define('DB_PASSWORD', 'your_password');
```
Set up directory permissions:
```bash
chmod 755 concour/
```
Directory Structure
```bash
Copy code
├── admin.php           # Admin authentication
├── ci.php              # Engineering applications
├── dashboard.php       # Admin dashboard
├── db_connection.php   # Database connection
├── mst.php             # Master's applications
├── concour/            # Excel file storage
└── assets/             # CSS/JS files
```
# Security Features

- Session-based authentication.
- Password hashing.
- CSRF protection.
- Rate limiting for login attempts.
- Input validation.
- File upload restrictions.

# Usage

- Access the admin panel: /admin.php
- Engineering applications: /ci.php
- Master's applications: /mst.php
- Dashboard: /dashboard.php

# Contributing

Fork the repository.
Create a feature branch:
```bash
git checkout -b feature/AmazingFeature
```
Commit your changes:
```bash
git commit -m 'Add AmazingFeature'
```
Push to the branch:
```bash
git push origin feature/AmazingFeature
```
Open a Pull Request.

# Authors

MOHAMED EL GORRIM (@MedGm)

# Acknowledgments

FSTT Administration.
Contributors and testers.

# Support

For support, please contact: elgorrim.mohamed@etu.uae.ac.ma
