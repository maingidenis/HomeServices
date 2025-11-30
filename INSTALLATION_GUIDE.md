# HomeServices - Simple Installation Guide for XAMPP

## 🚀 Quick Start (5 Minutes)

This guide is designed for **anyone** to install and run the HomeServices application, even without technical background.

---

## 📦 What You Need

1. **XAMPP** - Download from: https://www.apachefriends.org/
2. **This project files** - Download from GitHub

---

## 📍 Step 1: Install XAMPP

### For Windows:

1. **Download XAMPP**
   - Go to https://www.apachefriends.org/
   - Click "Download" for Windows
   - Choose the latest version (PHP 8.x)

2. **Run the installer**
   - Double-click the downloaded file
   - Click "Next" through all steps
   - Install to default location: `C:\xampp`
   - **Check these boxes during installation:**
     - ☑ Apache
     - ☑ MySQL
     - ☑ PHP
     - ☑ phpMyAdmin

3. **Start XAMPP**
   - Open "XAMPP Control Panel" from Start Menu
   - Click **"Start"** next to Apache
   - Click **"Start"** next to MySQL
   - Both should show green "Running" status

---

## 📁 Step 2: Copy Project Files

1. **Download the project**
   - Go to: https://github.com/maingidenis/HomeServices
   - Click green **"Code"** button
   - Click **"Download ZIP"**
   - Extract the ZIP file

2. **Copy to XAMPP**
   - Open `C:\xampp\htdocs\` folder
   - Create a new folder called `HomeServices`
   - Copy ALL project files into `C:\xampp\htdocs\HomeServices\`

**Your folder structure should look like:**
```
C:\xampp\htdocs\HomeServices\
    ├── app/
    ├── config/
    ├── public/
    ├── database_updates.sql
    └── ... (other files)
```

---

## 💾 Step 3: Setup Database (2 Minutes)

### Method 1: Using phpMyAdmin (Easiest)

1. **Open phpMyAdmin**
   - Open your web browser
   - Go to: `http://localhost/phpmyadmin`

2. **Create Database**
   - Click **"New"** in the left sidebar
   - Database name: `homeservices`
   - Collation: `utf8mb4_general_ci`
   - Click **"Create"**

3. **Import Database Schema**
   - Click on the `homeservices` database (left sidebar)
   - Click **"Import"** tab at the top
   - Click **"Choose File"**
   - Select `home_services.sql` from your HomeServices folder
   - Scroll down and click **"Import"**
   - Wait for "Import has been successfully finished" message ✅

### Method 2: Using XAMPP Shell (Alternative)

1. In XAMPP Control Panel, click **"Shell"**
2. Type these commands:
```bash
mysql -u root -p
```
3. Press Enter (no password needed by default)
4. Type:
```sql
CREATE DATABASE homeservices;
USE homeservices;
source C:/xampp/htdocs/HomeServices/home_services.sql
EXIT;
```

---

## ⚙️ Step 4: Configure the Application (1 Minute)

1. **Open the database config file**
   - Go to: `C:\xampp\htdocs\HomeServices\config\`
   - Open `Database.php` with Notepad

2. **Check these settings** (should already be correct):
```php
private $host = "localhost";
private $db_name = "homeservices";
private $username = "root";
private $password = ""; // Empty for XAMPP
```

3. **Save and close**

---

## ▶️ Step 5: Run the Application

1. **Make sure XAMPP is running:**
   - Open XAMPP Control Panel
   - Apache should be green/running
   - MySQL should be green/running

2. **Open in Browser:**
   - Open your web browser (Chrome, Firefox, Edge)
   - Go to: `http://localhost/HomeServices/public/index.php`
   - OR: `http://localhost/HomeServices/public/login.php`

3. **You should see the HomeServices login page! 🎉**

---

## 👤 Step 6: Create First Account

1. Click **"Register"** or go to: `http://localhost/HomeServices/public/register.php`
2. Fill in the registration form:
   - Name: Your name
   - Email: your@email.com
   - Password: (choose a password)
   - Role: Choose "Client" or "Provider"
3. Click **"Register"**
4. You can now login with your email and password

---

## ✅ Testing Your Installation

### Quick Test Checklist:

- ☑ Can you access: `http://localhost/HomeServices/public/login.php` ?
- ☑ Can you see the login page without errors?
- ☑ Can you register a new account?
- ☑ Can you login successfully?
- ☑ Can you access the dashboard after login?

If YES to all = **Installation Successful!** 🎉

---

## ⚠️ Common Issues & Solutions

### Issue 1: "Apache won't start"
**Solution:**
- Port 80 is being used by another program (Skype, IIS)
- Open XAMPP Control Panel
- Click **"Config"** next to Apache
- Click **"httpd.conf"**
- Find: `Listen 80`
- Change to: `Listen 8080`
- Save and restart Apache
- Now use: `http://localhost:8080/HomeServices/public/`

### Issue 2: "MySQL won't start"
**Solution:**
- Port 3306 is being used
- Stop any other MySQL services
- Or change MySQL port in XAMPP config

### Issue 3: "Can't connect to database"
**Solution:**
- Make sure MySQL is green/running in XAMPP
- Check `config/Database.php` settings
- Username should be: `root`
- Password should be: empty (blank)

### Issue 4: "Page not found (404)"
**Solution:**
- Check folder location: Must be in `C:\xampp\htdocs\HomeServices\`
- Check URL: Should be `http://localhost/HomeServices/public/index.php`
- Make sure Apache is running

### Issue 5: "White screen or PHP errors"
**Solution:**
- Check PHP version (needs PHP 7.4 or higher)
- Make sure all files were copied correctly
- Check file permissions

---

## 🚪 Daily Usage

### Starting the Application:

1. Open **XAMPP Control Panel**
2. Click **"Start"** for Apache
3. Click **"Start"** for MySQL
4. Wait for both to show green
5. Open browser: `http://localhost/HomeServices/public/index.php`

### Stopping the Application:

1. Open **XAMPP Control Panel**
2. Click **"Stop"** for Apache
3. Click **"Stop"** for MySQL
4. Close XAMPP Control Panel

**Note:** You can keep XAMPP running in the background if you use it frequently.

---

## 💻 Access Points

| Page | URL |
|------|-----|
| Home/Dashboard | `http://localhost/HomeServices/public/index.php` |
| Login | `http://localhost/HomeServices/public/login.php` |
| Register | `http://localhost/HomeServices/public/register.php` |
| Services | `http://localhost/HomeServices/public/service.php` |
| Appointments | `http://localhost/HomeServices/public/appointment.php` |
| phpMyAdmin | `http://localhost/phpmyadmin` |

---

## 📧 Default Test Account (Optional)

After installation, you can create a test account:

- **Email:** admin@homeservices.com
- **Password:** admin123
- **Role:** Admin

Create it through the registration page first time.

---

## 📚 Additional Resources

- **XAMPP Documentation:** https://www.apachefriends.org/docs/
- **PHP Tutorial:** https://www.w3schools.com/php/
- **MySQL Tutorial:** https://www.w3schools.com/mysql/

---

## 🔒 Security Notes for Production

**⚠️ This setup is for LOCAL TESTING ONLY**

Before deploying to a live server:

1. Change MySQL root password
2. Update database credentials in `config/Database.php`
3. Enable HTTPS/SSL
4. Set proper file permissions
5. Remove test accounts
6. Configure firewalls
7. Enable error logging

---

## 👥 Need Help?

1. Check the `IMPLEMENTATION_GUIDE.md` for technical details
2. Check XAMPP forums: https://community.apachefriends.org/
3. Review project documentation in the repository

---

## 🎓 Learning Path (Optional)

Want to understand how it works?

1. **Week 1:** Learn basic HTML/CSS
2. **Week 2:** Learn PHP basics
3. **Week 3:** Learn MySQL/SQL
4. **Week 4:** Understand MVC pattern

Resources: W3Schools, PHP.net, YouTube tutorials

---

**Installation Complete! 🎉**

You now have a fully functional HomeServices application running on your computer.

**Next Steps:**
- Create your account
- Explore the features
- Add services
- Make test bookings
- Check the admin panel

Enjoy using HomeServices! 🏠✨
