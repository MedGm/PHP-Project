# FSTT Student Application Management System <img src="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" alt="FSTT Logo" width="30" height="30">


## Overview
# Student Data Management System

This project is a web-based application for managing student data. It includes functionalities such as processing Excel files, user authentication, and interactive dashboards. The system is built using PHP and incorporates third-party libraries for advanced features like email handling and Excel processing.

------

## Features
- **Authentication:** Secure Admin login and session management (Rate limiting for login attempts) .
- **Excel Processing:** Excel file management and process student data from Excel files.
- **Dashboard:** Interactive dashboard for visualizing and managing data.
- **Student application processing for:**
  - **Engineering programs**: LSI, GI, GEO, GEMI, GA.
  - **Master's programs**: SE, AISD, ITBD, GC, GE, MMSD.
- **Email Notifications:** Send emails using PHPMailer.
- **Responsive Design:** Styled using CSS for an engaging user interface.
---

## Technologies
- **PHP 8.1+**
- **PHPSpreadsheet**
- **PHPMailer**
- **HTML/CSS & Tailwind CSS/JavaScript/Bootstrap**
- **Bootstrap**
- **AJAX**

---
## Prerequisites

Before running the project, ensure the following are installed:

- **PHP** (>= 7.4)
- **Composer** (for dependency management)
- A web server like **Apache** or **Nginx**
- **MySQL** or another compatible database

---

## Installation

Follow these steps to set up the project:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/MedGm/PHP-Project.git
   cd miniprojet1.2
   ```

2. **Install Dependencies**:
   Run the following command to install the required PHP libraries:
   ```bash
   composer install 
   ```

3. **Set Up Database**:
   - Create a MySQL database.
   - Configure the database connection in `includes/config.php`.

4. **Set File Permissions**:
   Ensure the `concour` directory has the necessary permissions for file uploads:
   ```bash
   chmod -R 775 concour
   ```

5. **Run the Application**:
   Use a local server to serve the project:
   ```bash
   php -S localhost:8000
   ```
   Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Project Structure

```
.
├── .vscode/                 # VSCode configuration files
├── concour/                 # Directory for Excel files
├── includes/                # PHP service and configuration files
│   ├── auth_service.php
│   ├── config.php
│   ├── session_manager.php
│   ├── header.php
│   ├── footer.php
├── vendor/                  # Third-party libraries (Composer dependencies)
│   ├── phpoffice/phpspreadsheet
│   ├── phpmailer/phpmailer
│   ├──...
├── admin.php                # Admin panel script
├── auth.php                 # Authentication script
├── dashboard.php            # Main dashboard for coordinators
├── dashboard2.php           # dashboard for superadmin
├── db_connection.php        # Database connection script
├── process_excel.php        # Excel file processing script
├── index.php                # Main page
├── student.php              # for student registration
├── mst.php/ci.php           # another page for student registration
├── download.php             # for downloading registration form
├── profile.php              # handling admins profile
├── style.css                # Main stylesheet
├── style2.css               # Additional styles
├── script.js                # JavaScript for dynamic behavior
├── students/charts/excel_files.php
```

---

## Dependencies

The project uses the following libraries:

- **[phpoffice/phpspreadsheet](https://phpspreadsheet.readthedocs.io/en/latest/)**: For handling Excel files.
- **[phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer)**: For sending emails.

Install these libraries via Composer:
```bash
composer require phpoffice/phpspreadsheet phpmailer/phpmailer
```

---

# Security Features

- Session-based authentication.
- Password hashing.
- CSRF protection.
- Rate limiting for login attempts.
- Input validation.
- File upload restrictions.

## Usage

**for Admins** : View the processed data and manage student records in the admin dashboard.

---
## License

This project is licensed under the [MIT License](LICENSE).

---

## Contributing

Feel free to submit issues or pull requests for improvements. Contributions are welcome!

---

# Authors

MOHAMED EL GORRIM (@MedGm)

# Acknowledgments

FSTT Administration.
Contributors and testers.

## Contact

For any questions or support, please contact:
**[Mohamed El Gorrim](mailto:elgorrim.mohamed@etu.uae.ac.ma)**
