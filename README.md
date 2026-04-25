# VolleyCup 4.0

> The official website for the premier intercollegiate volleyball tournament at ENSI, built with HTML, CSS, and a PHP registration flow.

---

## Preview

| Home | Schedule | Register | Teams |
|------|----------|----------|-------|
| Hero section with full-screen background, stats, and gallery | Single-day tournament timeline with highlighted events | PHP-backed registration form with validation and saved submissions | Featured university teams |

---

## About The Project

VolleyCup 4.0 is the official website for a university volleyball tournament organized at ENSI (National School of Computer Science), Tunisia. The site gives students and university teams everything they need, from discovering the event and viewing the schedule to registering their team online.

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
|-- data/
|   |-- registrations.json
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

Run the project with PHP's built-in development server:

```bash
git clone https://github.com/your-username/volleycup.git
cd volleycup
php -S localhost:8000
```

Then open [http://localhost:8000/home.html](http://localhost:8000/home.html).

Note: the PHP registration flow will not work if you only double-click the files and open them directly from disk.

---

## Features

- Full-screen landing page with event highlights
- Schedule page for tournament day planning
- Static registration page with a PHP submission endpoint
- Registrations saved locally in `data/registrations.json`
- Confirmation page with saved submission details
- Registration cancellation flow

---

## Team

| Name | Role |
|------|------|
| Syrine Borchani | Co-organizer and Developer |
| Mohamed Mokhtar Khaled | Co-organizer and Developer |
| Lamiss Dachraoui | Co-organizer and Developer |

---
