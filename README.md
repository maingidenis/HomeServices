# 🏠 HomeServices - Contact Tracing & Service Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Active-success)](https://github.com/maingidenis/HomeServices)

## 📋 Table of Contents
- [About the Project](#about-the-project)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Security Features](#security-features)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About the Project

**HomeServices** is a secure, full-featured web application designed for **home service management** and **contact tracing**. The system enables service providers to manage appointments, track visits, and facilitate contact tracing for health and safety compliance (e.g., COVID-19 protocols).

### Purpose
- **Service Management**: Book, manage, and track home services (cleaning, repairs, healthcare)
- **Contact Tracing**: Log visitor information for health and safety compliance
- **Multi-Role System**: Supports Admin, Provider, and Client user roles
- **Secure Authentication**: Implements MFA/2FA via email OTP verification

### Use Cases
- Healthcare home visit tracking
- Home maintenance service booking
- Disability support service coordination
- Contact tracing for COVID-19 or infectious disease management

---

## ✨ Key Features

### 🔐 Security & Authentication
- **Multi-Factor Authentication (MFA)**: Email OTP verification using PHPMailer
- **Role-Based Access Control (RBAC)**: Admin, Provider, and Client roles
- **Input Validation & Sanitization**: XSS and injection attack prevention
- **Parameterized Queries**: SQL injection protection via PDO
- **Password Hashing**: Bcrypt encryption for user passwords
- **Session Management**: Secure session handling with MFA flags

### 👥 User Management
- User registration with role selection
- Strong password policy enforcement
- Admin dashboard for user management
- Profile and settings pages

### 📅 Service & Appointment Management
- Service provider registration
- Service listing and booking
- Appointment scheduling and tracking
- Provider dashboard for managing bookings

### 📝 Contact Tracing & Visit Logs
- Visit log creation and tracking
- Health status updates
- Notification system for alerts
- Visit history and contact tracing reports

### 🎨 User Interface
- Responsive Bootstrap 5 design
- Mobile-friendly layouts
- Role-specific dashboards
- Intuitive navigation

---

## 🛠️ Tech Stack

### Backend
- **PHP 8.0+**: Server-side logic
- **MySQL 5.7+/MariaDB**: Database management
- **PDO**: Database abstraction layer
- **Composer**: Dependency management
- **PHPMailer**: Email OTP delivery via SMTP

### Frontend
- **HTML5**: Markup
- **CSS3**: Styling
- **Bootstrap 5.3**: Responsive UI framework
- **Bootstrap Icons**: Icon library
- **JavaScript**: Client-side interactivity

### Architecture
- **MVC Pattern**: Model-View-Controller architecture
- **Front Controller**: Single entry point routing via `index.php`
- **Middleware**: Authentication and authorization guards
- **Service Layer**: Business logic separation (e.g., Mailer service)

### Integrations
- **OAuth 2.0**: Google & Facebook login (optional)
- **Stripe API**: Payment processing (optional)
- **SMTP**: Gmail/custom SMTP for email delivery

---

## 💻 System Requirements

### Server Requirements
- **PHP**: 8.0 or higher
- **MySQL**: 5.7+ or MariaDB 10.3+
- **Apache/Nginx**: Web server with mod_rewrite enabled
- **Composer**: 2.0+
- **SMTP Server**: Gmail or custom SMTP for email OTP

### Recommended Development Environment
- **XAMPP 8.0+** (Windows/macOS/Linux)
- **MAMP** (macOS)
- **Docker** (with PHP 8.0 and MySQL containers)
- **VS Code** with PHP extensions

### PHP Extensions Required
```bash
- PDO
- pdo_mysql
- mbstring
- openssl
- curl
- fileinfo
- session
```

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/maingidenis/HomeServices.git
cd HomeServices
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Configure Database

#### Create MySQL Database
```sql
CREATE DATABASE home_services;
```

#### Import Database Schema
```bash
mysql -u root -p home_services < home_services.sql
```

Or use the update script:
```bash
mysql -u root -p home_services < database_updates.sql
```

#### Configure Database Connection
Edit `config/Database.php`:
```php
private $host = "localhost";
private $dbname = "home_services";
private $username = "root";
private $password = ""; // Your MySQL password
```

### 4. Configure Email (PHPMailer)

Edit `app/services/Mailer.php`:
```php
$this->mail->Username = 'your-email@gmail.com';
$this->mail->Password = 'your-app-password'; // Gmail App Password
```

**Note**: For Gmail, enable 2FA and generate an [App Password](https://support.google.com/accounts/answer/185833).

### 5. Set Up Virtual Host (Optional)

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName homeservices.local
    DocumentRoot "/path/to/HomeServices/public"
    <Directory "/path/to/HomeServices/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `/etc/hosts`:
```
127.0.0.1 homeservices.local
```

### 6. Start the Application

#### Using XAMPP
1. Move project to `htdocs/HomeServices`
2. Start Apache and MySQL
3. Access: `http://localhost/HomeServices/public/index.php`

#### Using PHP Built-in Server
```bash
cd public
php -S localhost:8000
```
Access: `http://localhost:8000`

---

## 🔒 Security Features

### 1. Input Validation & Sanitization
- `strip_tags()` for XSS prevention
- `filter_var()` for email validation
- Regex patterns for password strength
- Whitelist validation for role selection
- `htmlspecialchars()` for output escaping

### 2. Multi-Factor Authentication (MFA/2FA)
- 6-digit OTP generation via `random_int()`
- Time-limited OTP (5-minute expiration)
- Secure email delivery via PHPMailer SMTP
- Session-based MFA tracking

### 3. Role-Based Access Control (RBAC)
- Three roles: Admin, Provider, Client
- Middleware guards for protected routes
- Session-based role enforcement
- Admin-only pages with access restrictions

### 4. SQL Injection Prevention
- 100% parameterized queries via PDO
- Prepared statements for all database operations
- No raw SQL with user input

---

## 📁 Project Structure

```
HomeServices/
├── app/
│   ├── controllers/          # Business logic handlers
│   │   ├── UserController.php
│   │   ├── AppointmentController.php
│   │   ├── ServiceController.php
│   │   ├── NotificationController.php
│   │   └── VisitLogController.php
│   ├── models/               # Database interaction layer
│   │   ├── User.php
│   │   ├── Appointment.php
│   │   ├── Service.php
│   │   ├── VisitLog.php
│   │   └── Notification.php
│   ├── middleware/           # Authentication & authorization
│   │   ├── auth.php
│   │   ├── admin_only.php
│   │   └── mfa.php
│   └── services/             # Business services
│       └── Mailer.php        # PHPMailer email service
├── config/
│   ├── Database.php          # Database connection
│   ├── oauth.php             # OAuth configuration
│   └── stripe.php            # Payment configuration
├── public/                   # Web root (entry point)
│   ├── index.php             # Front controller (routing)
│   ├── login.php             # Login page
│   ├── register.php          # Registration page
│   ├── dashboard.php         # Main dashboard
│   ├── appointment.php       # Appointment management
│   ├── service.php           # Service management
│   ├── visitlog.php          # Visit log tracking
│   ├── notification.php      # Notifications
│   ├── verify_otp.php        # OTP verification
│   ├── admin/                # Admin pages
│   ├── client/               # Client pages
│   ├── provider/             # Provider pages
│   ├── includes/             # Shared components
│   │   ├── header.php
│   │   ├── navbar.php
│   │   ├── footer.php
│   │   ├── profile.php
│   │   └── settings.php
│   ├── css/                  # Stylesheets
│   └── assets/               # Static files
├── vendor/                   # Composer dependencies
├── composer.json             # Dependency definitions
├── home_services.sql         # Database schema
├── database_updates.sql      # Schema updates
└── README.md                 # This file
```

---

## 📖 Usage

### Default Login Credentials

After installation, register the first user as an **Admin**. Only one admin account is allowed per system.

### User Roles

1. **Admin**
   - Full system access
   - User management
   - Service oversight
   - System configuration
   - Access: `admin/dashboard.php`

2. **Provider**
   - Service management
   - Appointment handling
   - Visit log creation
   - Access: `provider/dashboard.php`

3. **Client**
   - Service booking
   - Appointment tracking
   - Notification viewing
   - Access: `index.php?page=dashboard`

### Workflow Example

1. **Registration**: User registers as Client, Provider, or Admin
2. **Login**: User enters credentials
3. **MFA Verification**: System sends 6-digit OTP via email
4. **OTP Entry**: User enters OTP on verification page
5. **Dashboard Access**: User redirected to role-based dashboard
6. **Service Booking**: Client browses services and books appointments
7. **Visit Logging**: Provider logs visits for contact tracing
8. **Notifications**: System sends alerts for appointments/visits

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Test all changes locally
- Document new features in README
- Ensure security best practices

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📞 Support & Contact

- **Issues**: [GitHub Issues](https://github.com/maingidenis/HomeServices/issues)
- **Discussions**: [GitHub Discussions](https://github.com/maingidenis/HomeServices/discussions)

---

## 🙏 Acknowledgments

- **Bootstrap Team**: For the amazing UI framework
- **PHPMailer**: For reliable email delivery
- **Composer**: For dependency management
- **GitHub Community**: For open-source collaboration

---

## 🔮 Future Enhancements

- [ ] SMS OTP via Twilio
- [ ] Real-time notifications (WebSockets)
- [ ] Mobile app (React Native/Flutter)
- [ ] Advanced analytics dashboard
- [ ] Geolocation-based services
- [ ] Multi-language support
- [ ] API for third-party integrations
- [ ] Rate limiting for security
- [ ] CSRF token implementation

---

**Built with ❤️ for secure and efficient home service management**
