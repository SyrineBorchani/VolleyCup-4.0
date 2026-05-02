# VolleyCup 4.0

> The official website for the premier intercollegiate volleyball tournament at ENSI, built with HTML, CSS, JavaScript, PHP, and MySQL.

## Pages

| Page | File | Description |
|------|------|-------------|
| Home | `home.html` | Landing page with event highlights, gallery, and organizers |
| Schedule | `schedule.html` | Tournament day timeline from check-in to closing celebration |
| Register | `register.html` | Team registration form with live validation, team name support, optional services, and optional team photo upload |
| Success | `success.php` | Registration confirmation page with cancellation support |
| Teams | `teams.php` | Featured teams plus database-backed registered teams loaded with PDO |

## Features

- Team registration with client-side and server-side validation
- PDO-based MySQL persistence through a repository layer
- Required team name, university, captain, roster size, category, and contact details
- Optional team photo upload with image validation
- Confirmation page with full saved registration details
- Cancellation flow for submitted registrations
- Dynamic teams page showing saved registrations from the database

## Tech Stack

- HTML5
- CSS3
- Vanilla JavaScript
- PHP
- MySQL

## File Structure

```text
volleycup/
|-- home.html
|-- schedule.html
|-- register.html
|-- success.php
|-- teams.php
|-- submit_registration.php
|-- add_test_team.php
|-- update_test_registration.php
|-- config/
|   `-- database.php
|-- database/
|   `-- volleycup4_setup.sql
|-- includes/
|   |-- Registration.php
|   |-- RegistrationRepository.php
|   `-- registration_repository.php
|-- src/
|   |-- assets/
|   |   |-- images/
|   |   `-- video/
|   `-- js/
|       |-- home.js
|       |-- register.js
|       `-- teams.js
|-- style.css
|-- style_header_footer.css
|-- schedule.css
```

## Getting Started

1. Place the project in your XAMPP `htdocs` directory.
2. Start Apache and MySQL from XAMPP.
3. Import `database/volleycup4_setup.sql` into phpMyAdmin.
4. Open `http://localhost/VolleyCup-4.0/home.html`.

Optional environment variables if you are not using the default XAMPP credentials:

```bash
VOLLEYCUP_DB_HOST=127.0.0.1
VOLLEYCUP_DB_PORT=3306
VOLLEYCUP_DB_NAME=volleycup4.0
VOLLEYCUP_DB_USER=root
VOLLEYCUP_DB_PASS=
```

## Notes

- The PHP registration flow must be served through Apache/XAMPP.
- `add_test_team.php` and `update_test_registration.php` are helper scripts for local testing.
