# VolleyCup 4.0

> The official website for the premier intercollegiate volleyball tournament at ENSI, built with HTML, CSS, and a PHP/MySQL registration flow.

---

## Pages

| Page | File | Description |
|------|------|-------------|
| Home | `home.html` | Hero, event highlights, gallery, and organizers |
| Schedule | `schedule.html` | Full tournament day timeline from check-in to closing celebration |
| Register | `register.html` | Static registration form that submits to a PHP endpoint |
| Success | `success.php` | Registration status page with cancellation support |
| Teams | `teams.html` | Participating university teams |

---

## Tech Stack

- HTML5
- CSS3
- PHP
- MySQL

---

## File Structure

```text
volleycup/
|-- home.html
|-- schedule.html
|-- teams.html
|-- register.html
|-- success.php
|-- submit_registration.php
|-- add_test_team.php
|-- database/
|   `-- volleycup4_setup.sql
|-- config/
|   `-- database.php
|-- includes/
|   `-- registration_repository.php
|-- src/
|   |-- assets/
|   |   |-- images/
|   |   |   |-- hero.jfif
|   |   |   |-- volleyball.png
|   |   |   |-- g1.jfif - g5.jfif
|   |   |   |-- syrine.jfif
|   |   |   |-- mokhtar.png
|   |   |   `-- lamiss.png
|   |   |-- video/
|   |   |   `-- Video.mp4
|   |-- js/
|   |   |-- home.js
|   |   `-- register.js
|-- style.css
|-- style_header_footer.css
|-- schedule.css
```

---

## Getting Started

Run the project through Apache and MySQL in XAMPP:

```bash
git clone https://github.com/your-username/volleycup.git
cd volleycup
```

1. Create a MySQL database named `volleycup4.0` in phpMyAdmin.
2. Make sure Apache and MySQL are running in XAMPP.
3. Serve this folder from XAMPP, by placing it inside `htdocs`.
4. Open `http://localhost/VolleyCup-4.0/home.html` if the folder is inside `htdocs`, or the matching local URL for your Apache setup.

Optional environment variables if you are not using the default XAMPP credentials:

```bash
VOLLEYCUP_DB_HOST=127.0.0.1
VOLLEYCUP_DB_PORT=3306
VOLLEYCUP_DB_NAME=volleycup4.0
VOLLEYCUP_DB_USER=root
VOLLEYCUP_DB_PASS=
```

Note: the PHP registration flow will not work if you only double-click the files and open them directly from disk.

## Friend Setup

If a teammate wants the project to work on their own computer at home:

1. Install XAMPP.
2. Copy this project into `xampp/htdocs/VolleyCup-4.0`.
3. Start Apache and MySQL in XAMPP.
4. Open phpMyAdmin and import [database/volleycup4_setup.sql](/D:/Web/VolleyCup-4.0/database/volleycup4_setup.sql).
5. Open `http://localhost/VolleyCup-4.0/register.html`.

This project is already configured for the usual XAMPP defaults:

- MySQL host: `127.0.0.1`
- MySQL port: `3306`
- Database: `volleycup4.0`
- User: `root`
- Password: empty

---

## Features

- Full-screen landing page with event highlights
- Schedule page for tournament day planning
- Team registration form with PHP validation
- Registrations saved in MySQL through XAMPP
- Confirmation page with saved submission details
- Registration cancellation flow

---
