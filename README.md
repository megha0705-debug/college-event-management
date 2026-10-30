# 🎓 CampusPulse — College Event Management Website

A modern, responsive, secure, and full-featured web application built for college event discovery, attendee registration, pass generation, and administrative lifecycle management.

![CampusPulse Hero](assets/images/techfest.svg)

---

## 🌐 Live Deployment & Online Access

- 🚀 **Live Demo URL:** [https://directed-request-acceptable-possible.trycloudflare.com](https://directed-request-acceptable-possible.trycloudflare.com)
- 🐙 **GitHub Repository:** [https://github.com/gmegha0711/college-event-management](https://github.com/gmegha0711/college-event-management)
- ☁️ **Permanent Cloud URL:** [https://campuspulse-events.onrender.com](https://campuspulse-events.onrender.com) *(Render.com)*

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Key Features](#key-features)
3. [Technology Stack](#technology-stack)
4. [Software Requirements](#software-requirements)
5. [Step-by-Step Installation & Setup (XAMPP)](#step-by-step-installation--setup-xampp)
   - [Step 1: Install & Start XAMPP](#step-1-install--start-xampp)
   - [Step 2: Place Project Files in `htdocs`](#step-2-place-project-files-in-htdocs)
   - [Step 3: Import the MySQL Database](#step-3-import-the-mysql-database)
   - [Step 4: Verify Database Connection](#step-4-verify-database-connection)
   - [Step 5: Launch the Website in Your Browser](#step-5-launch-the-website-in-your-browser)
6. [Default Demo Credentials](#default-demo-credentials)
7. [User Workflows](#user-workflows)
   - [Student Experience](#student-experience)
   - [Administrator Experience](#administrator-experience)
8. [Database Architecture](#database-architecture)
9. [Security Implementations](#security-implementations)
10. [Troubleshooting Common Issues](#troubleshooting-common-issues)
11. [Project File Structure](#project-file-structure)

---

## 1. Project Overview

**CampusPulse** solves the challenges of fragmented notice boards, manual paper rosters, and chaotic queue lines in university and college campuses. It unifies technical symposiums, cultural carnivals, coding hackathons, athletic championships, workshops, and leadership seminars into an intuitive, responsive digital portal.

### Objectives Achieved:
- **For Students:** Discover upcoming college events with category & date filters, check remaining seat capacity in real time, register with a single click, access digital QR passes, and cancel registrations when schedules change.
- **For Administrators & Faculty:** Create, edit, and schedule events, upload promotional banner artwork, enforce strict seat caps, set registration deadlines, view attendee rosters, update attendance status, and export registration data to CSV format.

---

## 2. Key Features

- **Navy Blue, Purple & White Modern Theme:** Clean typography (`Plus Jakarta Sans`), subtle gradients, card hover transformations, and interactive status badges.
- **Complete Responsiveness:** Tailored for seamless navigation on desktops, tablets, and mobile smartphones.
- **Category-Based Filtering & Search:** Technical, Cultural, Sports, Workshops, and Seminars.
- **Overbooking Prevention:** MySQL atomic transactions and row locking (`FOR UPDATE`) prevent registrations beyond capacity limits.
- **Automated Deadline Validation:** Server-side timestamp checks guarantee that closed events cannot accept late registrations.
- **Duplicate Registration Prevention:** Unique database constraints ensure a student cannot register twice for the same event.
- **Digital Event Entry Passes:** Realistic pass views with QR codes, attendee details, and instant print capability.
- **CSV Data Export:** One-click spreadsheet roster downloads for event conveners and faculty desks.
- **Role-Based Access Control:** Strict server-side authorization separating Administrator and Student capabilities.

---

## 3. Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework:** Bootstrap 5.3 & Google Fonts (`Plus Jakarta Sans`)
- **Iconography:** Font Awesome 6.5
- **Backend:** PHP 7.4 / 8.x (Object-Oriented PDO, prepared statements)
- **Database:** MySQL 5.7+ / MariaDB 10.4+
- **Local Server Environment:** XAMPP (Apache + MySQL / phpMyAdmin)

---

## 4. Software Requirements

Before getting started, ensure you have:
1. **XAMPP for Windows, macOS, or Linux** (Version 8.0 or higher recommended). Download from [Apache Friends](https://www.apachefriends.org/).
2. A modern web browser: Google Chrome, Mozilla Firefox, Microsoft Edge, or Safari.
3. (Optional) Code Editor: VS Code / Sublime Text for customizing college details.

---

## 5. Step-by-Step Installation & Setup (XAMPP)

### Step 1: Install & Start XAMPP
1. Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/).
2. Open the **XAMPP Control Panel**.
3. Click the **Start** buttons next to **Apache** and **MySQL**. Both modules will show green status indicators.

### Step 2: Place Project Files in `htdocs`
1. Navigate to your XAMPP installation directory, typically located at:
   - **Windows:** `C:\xampp\htdocs\`
   - **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/`
   - **Linux:** `/opt/lampp/htdocs/`
2. Copy this project folder into `htdocs` and name it `college-event-management`:
   ```
   C:\xampp\htdocs\college-event-management\
   ```
   *(Note: You can also keep it as `meghana.project` or any folder name — the built-in `base_url()` automatically detects subdirectories).*

### Step 3: Import the MySQL Database
1. Open your web browser and navigate to:
   ```
   http://localhost/phpmyadmin/
   ```
2. Click on the **Databases** tab in the top navigation.
3. In the "Create database" field:
   - Database name: `college_event_management`
   - Collation: `utf8mb4_unicode_ci`
   - Click **Create**.
4. Select the newly created `college_event_management` database from the left-hand sidebar.
5. Click on the **Import** tab in the top menu.
6. Click **Choose File** (or "Browse") and select the SQL file from your project:
   ```
   database/college_event_management.sql
   ```
7. Scroll to the bottom and click **Import** (or **Go**).
8. You will see a success message: *"Import has been successfully finished, 8 queries executed."*

### Step 4: Verify Database Connection
Open `config/database.php` in a text editor. By default, XAMPP uses:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'college_event_management');
define('DB_USER', 'root');
define('DB_PASS', ''); // default password is empty in XAMPP
```
If your MySQL has a custom root password, enter it in `DB_PASS`.

### Step 5: Launch the Website in Your Browser
Open your browser and visit:
```
http://localhost/college-event-management/
```
*(Or `http://localhost/meghana.project/` if you kept that folder name).*

---

## 6. Default Demo Credentials

The database script automatically seeds test accounts pre-configured with secure bcrypt password hashes. For instant testing, the login page also includes 1-click **"Demo Admin"** and **"Demo Student"** autofill buttons.

| Role | Email Address | Password | Description |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@college.edu` | `Admin@123` | Full access to create, edit, delete events, view registrations, export CSV |
| **Student (Demo 1)** | `aarav.sharma@student.college.edu` | `Student@123` | 3rd Year CSE student with sample registrations |
| **Student (Demo 2)** | `priya.patel@student.college.edu` | `Student@123` | 2nd Year IT student account |
| **Student (Demo 3)** | `marcus.chen@student.college.edu` | `Student@123` | 4th Year ECE student account |

---

## 7. User Workflows

### Student Experience
1. **Explore Events:** Browse upcoming symposiums on the homepage or directory.
2. **Search & Filter:** Filter by Technical, Cultural, Sports, Workshops, or Seminars, or search by venue and title.
3. **Registration:** Click "Register Now". If not logged in, you will be prompted to sign in or create an account with your College ID and Department.
4. **Instant Pass:** Once registered, view your digital ticket pass under **"My Registrations"** with a printable pass layout and verification code.
5. **Manage & Cancel:** If your schedule changes, you can cancel an upcoming registration with 1 click before the event date.
6. **Profile:** Update your phone number, department, and change your password securely.

### Administrator Experience
1. **Access Admin Portal:** Log in with `admin@college.edu` to access the protected Administrator Dashboard.
2. **Event Management (`admin/events.php`):**
   - Click **"Add New Event"** to specify title, description, category, venue, capacity, deadlines, and upload banner artwork.
   - Click **"Edit"** to adjust dates, capacity, or mark events as *Completed* or *Cancelled*.
   - Click **"Delete"** with a safety confirmation modal to permanently delete an event.
3. **Attendee Roster (`admin/registrations.php`):**
   - Filter attendees by event.
   - Search by student name or roll number.
   - Change registration status from `Confirmed` to `Attended` or `Cancelled`.
4. **CSV Export (`admin/export-csv.php`):**
   - Click **"Export to CSV"** to instantly stream a formatted spreadsheet report of all registered attendees.
5. **Students Directory (`admin/students.php`):**
   - View all registered students, their department, roll numbers, and total event participation counts.

---

## 8. Database Architecture

The schema uses foreign keys with `ON DELETE CASCADE` and unique constraints:

```sql
college_event_management
├── users (id, name, email, student_id, department, year_of_study, phone, password, role, created_at)
├── events (id, title, description, category, event_date, start_time, end_time, venue, organizer, image, capacity, registration_deadline, status, created_at)
├── registrations (id, user_id, event_id, registration_date, status)
└── contact_messages (id, name, email, subject, message, created_at)
```

- **Unique Constraint:** `UNIQUE KEY (user_id, event_id)` prevents a student from duplicate booking.
- **Indexes:** Applied on `email`, `student_id`, `role`, `event_date`, `category`, and `status` for rapid querying.

---

## 9. Security Implementations

- **SQL Injection Prevention:** Every query with dynamic parameters uses PHP Data Objects (PDO) with prepared statements and bound parameters.
- **Cross-Site Scripting (XSS) Mitigation:** All user inputs and database outputs rendered in HTML templates are sanitized via `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.
- **CSRF Protection:** State-changing forms (Registration, Event Creation, Editing, Deletion, Status changes) validate cryptographically secure random session tokens using `hash_equals()`.
- **Session Security:** Cookies configured with `HttpOnly`, `SameSite=Lax`, and `session_regenerate_id(true)` upon authentication to neutralize session fixation attacks.
- **Race Condition Prevention:** The event registration routine wraps capacity checking and record insertion inside a database transaction (`beginTransaction()`, `FOR UPDATE`, `commit()`).
- **File Upload Hardening:** Uploaded banners are verified using server-side MIME type detection (`finfo`), limited to 3MB, sanitized with random alphanumeric filenames, and isolated inside `assets/images/uploads/events/`.

---

## 10. Troubleshooting Common Issues

### Issue 1: "Database Connection Notice" appears on the website
- **Cause:** MySQL service is not running in XAMPP or the database has not been imported.
- **Solution:**
  1. Open the XAMPP Control Panel and verify that **MySQL** is running (green).
  2. Open `http://localhost/phpmyadmin/` and ensure the database named `college_event_management` exists and has 4 tables: `users`, `events`, `registrations`, `contact_messages`.

### Issue 2: "Access denied for user 'root'@'localhost'"
- **Cause:** Your XAMPP MySQL has a root password set.
- **Solution:** Open `config/database.php` and set your password:
  ```php
  define('DB_PASS', 'your_mysql_password_here');
  ```

### Issue 3: "Port 80 or 443 already in use" in XAMPP
- **Cause:** Another program (e.g. Skype, IIS, VMware) is using port 80.
- **Solution:** In XAMPP Control Panel, click **Config** on Apache > `httpd.conf`, change `Listen 80` to `Listen 8080`, and restart Apache. Then access the site at `http://localhost:8080/college-event-management/`.

### Issue 4: Image upload error "Failed to save file"
- **Cause:** Directory permissions on the uploads folder.
- **Solution:** Ensure `assets/images/uploads/events/` exists and has write permissions.

---

## 11. Project File Structure

```
college-event-management/
├── index.php                          # Homepage (Hero, Categories, Featured & Upcoming Events, Stats, Testimonials)
├── events.php                         # Events Directory with multi-filter & search
├── event-details.php                  # Event Details, Seat Counters & Registration
├── about.php                          # About College Portal, Committee, & FAQ
├── contact.php                        # Contact Form & Campus Information
├── login.php                          # Student & Administrator Login
├── register.php                       # Student Registration Form
├── logout.php                         # Secure Session Destruction
├── README.md                          # Full Project Documentation & Setup Guide
│
├── config/
│   └── database.php                   # PDO MySQL Database Configuration
│
├── includes/
│   ├── header.php                     # Shared HTML Navigation Header
│   ├── footer.php                     # Shared HTML Footer & Scripts
│   ├── auth.php                       # Session Auth & Role-Based Authorization
│   └── functions.php                  # Helper functions, XSS escape, CSRF tokens
│
├── student/
│   ├── dashboard.php                  # Student Dashboard & Overview
│   ├── my-registrations.php           # Active Registrations & Digital QR Passes
│   ├── profile.php                    # Student Profile & Password Settings
│   └── cancel-registration.php        # Cancel Event Registration Handler
│
├── admin/
│   ├── dashboard.php                  # Administrator Overview & Stats Widgets
│   ├── events.php                     # Manage, Filter, & Delete Events Table
│   ├── add-event.php                  # Create New Event Form with Banner Upload
│   ├── edit-event.php                 # Edit Event Details & Replace Banner
│   ├── delete-event.php               # Safe Event Deletion Handler
│   ├── registrations.php              # Attendee Roster & Attendance Status
│   ├── export-csv.php                 # Downloadable Attendee CSV Generator
│   └── students.php                   # Registered Student Directory
│
├── assets/
│   ├── css/
│   │   └── style.css                  # Custom Navy/Purple Modern Responsive Theme
│   ├── js/
│   │   └── script.js                  # Form validation, file previews, delete modal
│   └── images/
│       ├── logo.svg                   # College CampusPulse Logo
│       ├── techfest.svg               # TechFest 2026 Vector Banner
│       ├── cultural-fiesta.svg        # Cultural Fiesta 2026 Vector Banner
│       ├── hackathon.svg              # CodeSprint 2026 Vector Banner
│       ├── sports-carnival.svg        # Sports Carnival 2026 Vector Banner
│       ├── ai-workshop.svg            # AI Masterclass Vector Banner
│       ├── seminar.svg                # Startup Seminar Vector Banner
│       ├── robowars.svg               # RoboWars Vector Banner
│       ├── webdev.svg                 # Web Dev Summit Vector Banner
│       ├── default-event.svg          # Fallback Vector Banner
│       └── uploads/events/            # Custom Admin Banner Uploads
│
└── database/
    └── college_event_management.sql   # Complete Schema & Seed Data SQL Script
```

---

*Designed and Developed for College Event Management.*

