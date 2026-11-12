# LearnSphere

A Laravel Learning Management System (LMS) for courses, progress tracking, quizzes, assignments, certificates, and simulated payments.

## Purpose

Help admins, instructors, and learners manage teaching and learning in one place — from course authoring to enrollment, assessments, and certificates.

## Technology stack

- PHP 8.3+
- Laravel 13
- MySQL / MariaDB
- Blade + Bootstrap / Tabler UI

## Features

- Role-based access (Admin, Instructor, Learner, Content Manager, Support)
- Course catalogue, sections, lessons, and progress tracking
- Quizzes, assignments, discussions, announcements
- Certificates with public verification
- Simulated course checkout (no real payments)
- Module guides in the UI for each screen

## Current status

Fully functional local demo. Ready to clone, set up database, and run.

## Database

- Database name: `learnsphere_db`
- Schema is created by Laravel migrations
- Demo data is added by seeders
- No database dump is included in this repository

## Folder structure

```text
app/                 Controllers, models, services
database/            Migrations and seeders
project-details/     Specs and SQL reference
public/              Public assets (CSS/JS)
resources/views/     Blade UI
routes/              Web and API routes
tests/               Feature tests
```

## Setup (clone and run)

### 1) Requirements

- PHP 8.3+
- Composer
- MySQL / MariaDB

### 2) Clone and install

```bash
git clone <your-repo-url>
cd learnsphere
composer install
copy .env.example .env
php artisan key:generate
```

On Linux/macOS use `cp .env.example .env` instead of `copy`.

### 3) Create database

Create an empty MySQL database named `learnsphere_db`, then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learnsphere_db
DB_USERNAME=root
DB_PASSWORD=
```

Use your real MySQL port/password if different (for example `3307` on some WAMP setups).

### 4) Migrate, seed, and start

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Demo login

Password for all accounts: `password`

| Role | Email |
|------|--------|
| Super Admin | `admin@learnsphere.test` |
| Instructor | `instructor@learnsphere.test` |
| Learner | `learner@learnsphere.test` |
| Content Manager | `content@learnsphere.test` |
| Support | `support@learnsphere.test` |

Demo accounts are also shown on the login page.
