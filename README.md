# 🏐 VolleyCup 4.0

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

> 🎓 **Academic Project** | 🏐 **Intercollegiate Volleyball Tournament** | 🇹🇳 **ENSI, Tunisia**

**VolleyCup 4.0** is the official website for the premier intercollegiate volleyball tournament organized at ENSI — the National School of Computer Science. It provides a full tournament experience: event highlights, schedule, participating teams, and a complete team registration flow backed by PHP and MySQL.

---

> 🛠️ **Current Status**: Fully functional registration flow with PHP validation, MySQL persistence, confirmation and cancellation pages, and a live team summary dashboard.

---

## 📸 Pages Overview

### 🏠 Home (`home.html`)
The landing page features a full-screen hero section with event highlights, an animated statistics block, a phone mockup gallery, a photo gallery, and an organizers section.

### 📅 Schedule (`schedule.html`)
A full tournament day timeline from check-in through to the closing celebration, giving participants a clear picture of the day's flow.

### 🏆 Teams (`teams.html`)
Displays all participating university teams with a live ticker, spotlight modal per team, search and sort toolbar, and a scroll-to-top button.

### 📋 Register (`register.html`)
A multi-field registration form with client-side live validation, a reset button, optional services checkboxes, and a sound effect on successful submission. Fields are organized as:

| Row | Left | Right |
|-----|------|-------|
| 1 | Team Name | Roster Size |
| 2 | University Name | Team Captain |
| 3 | Contact Phone | Team Contact Email |
| 4 | — | Team Category (Men / Women / Mixed) |

### ✅ Success (`success.php`)
Displays full registration details after submission. Supports a cancellation flow with visual state changes for confirmed vs cancelled registrations.

---

## ✨ Key Features

- 📝 **Team Registration Form** — Full client-side and server-side validation with live field feedback and sound feedback on submit.
- 🗄️ **PHP + MySQL Backend** — Registrations saved to MySQL via PDO prepared statements with a clean repository pattern.
- ✅ **Confirmation & Cancellation** — Every registration gets a unique ID and a dedicated status page with a one-click cancel flow.
- 🏐 **Teams Page** — Live ticker, spotlight overlay, search/filter toolbar, and smooth scroll-to-top.
- 📊 **Category Summary** — `team_summary.php` groups confirmed teams by category using PHP array functions and echo output.
- 🎨 **Consistent Design System** — Dark theme with orange accent (`#ff5a00`), shared header/footer, and smooth animations throughout.

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| **HTML5** | Semantic page structure |
| **CSS3** | Layout (Grid, Flexbox), animations, dark theme |
| **Vanilla JavaScript** | Live validation, form submission via Fetch API, audio feedback |
| **Native PHP** | Server-side validation, PDO, registration repository pattern |
| **MySQL** | Relational storage via XAMPP |

---

## 📁 File Structure

```text
📦 VolleyCup-4.0/
│
├── 🌐 home.html
├── 📅 schedule.html
├── 🏆 teams.html
├── 📋 register.html
├── ✅ success.php
├── submit_registration.php
├── add_test_team.php
├── update_test_registration.php
│
├── 📁 config/
│   └── database.php
│
├── 📁 includes/
│   └── registration_repository.php
│
├── 📁 database/
│   └── volleycup4_setup.sql
│
├── 📁 src/
│   ├── assets/
│   │   ├── images/
│   │   │   ├── hero.jfif
│   │   │   ├── volleyball.png
│   │   │   ├── g1.jfif - g5.jfif
│   │   │   ├── syrine.jfif
│   │   │   ├── mokhtar.png
│   │   │   └── lamiss.png
│   │   └── video/
│   │       └── Video.mp4
│   └── js/
│       ├── home.js
│       └── register.js
│
├── style.css
├── style_header_footer.css
└── schedule.css
```

---

## 🚀 Getting Started

This project runs through Apache and MySQL in XAMPP.

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed
- A modern browser (Chrome, Firefox, Edge)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/SyrineBorchani/VolleyCup-4.0.git
   cd VolleyCup-4.0
   ```

2. **Place the folder in XAMPP**
   ```
   xampp/htdocs/VolleyCup-4.0/
   ```

3. **Start Apache and MySQL** in the XAMPP Control Panel.

4. **Import the database**
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Import `database/volleycup4_setup.sql`

5. **Open the project**
   ```
   http://localhost/VolleyCup-4.0/home.html
   ```

### Optional Environment Variables
Only needed if you are not using default XAMPP credentials:

```bash
VOLLEYCUP_DB_HOST=127.0.0.1
VOLLEYCUP_DB_PORT=3306
VOLLEYCUP_DB_NAME=volleycup4.0
VOLLEYCUP_DB_USER=root
VOLLEYCUP_DB_PASS=
```

> ⚠️ The PHP registration flow will not work if you open files directly from disk. Always serve through XAMPP.

---

## 👥 Friend / Teammate Setup

1. Install XAMPP.
2. Copy this project into `xampp/htdocs/VolleyCup-4.0`.
3. Start Apache and MySQL in XAMPP.
4. Open phpMyAdmin and import `database/volleycup4_setup.sql`.
5. Open `http://localhost/VolleyCup-4.0/home.html`.

Default XAMPP credentials already configured in the project:

| Setting | Value |
|---------|-------|
| Host | `127.0.0.1` |
| Port | `3306` |
| Database | `volleycup4.0` |
| User | `root` |
| Password | *(empty)* |

---

## 🔄 Registration Flow

```
register.html  →  register.js (live validation + Fetch API)
      ↓
submit_registration.php (PHP server-side validation)
      ↓
registration_repository.php (PDO INSERT)
      ↓
MySQL registrations table
      ↓
success.php?id=... (confirmation + cancel button)
```

---

## 🧪 Dev Helpers

| File | Purpose | How to use |
|------|---------|-----------|
| `add_test_team.php` | Insert a test registration | Visit in browser |
| `update_test_registration.php` | Update a registration via URL params | `?id=abc&teamName=Thunder&uni=ENSI&roster=10&category=mixed` |

---

## 🎓 Academic Project Notice

This is a non-commercial educational project built for a university web development course at ENSI — the National School of Computer Science, Tunisia. It is not intended for commercial use.

---

## 🙏 Special Thanks

Built with passion at ENSI, Tunisia. 🏐🇹🇳