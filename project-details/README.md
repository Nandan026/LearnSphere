# LearnSphere — Laravel Learning Management System

**Framework is Laravel, not CodeIgniter.** LearnSphere is a portfolio-grade LMS inspired by Moodle, Canvas and Open edX. It combines course authoring, learner progress, assessments, assignments, communication, credentials, simulated commerce and analytics.

## Product research and feature inspiration

- **Moodle:** activities, quizzes, assignments, forums, completion tracking and mobile-friendly learning. [Moodle app features](https://support.moodle.com/support/solutions/articles/80000985017-what-can-i-do-with-the-moodle-app-)
- **Canvas:** modules, assignments, discussions, quizzes, gradebook, rubrics, feedback, announcements and analytics. [Canvas overview](https://community.instructure.com/en/kb/canvas-lms-basics-guide) · [Feature comparison](https://community.instructure.com/en/kb/articles/664412)
- **Open edX:** course authoring, reusable learning sequences, assessment types, credentials, learner analytics, integrations and self-paced/instructor-led learning. [Platform](https://openedx.org/platform/) · [Features](https://openedx.org/platform/features/)

These are research references and inspiration, not a promise to reproduce every enterprise feature.

## Product vision

A responsive platform with four core experiences:
1. **Admin:** users, roles, categories, course moderation, platform settings, reports and audit history.
2. **Instructor:** course builder, sections, lessons, resources, question banks, quizzes, assignments, announcements, discussions, grading and course analytics.
3. **Learner:** catalogue, enrollment, course player, progress, bookmarks, quizzes, assignment submissions, discussions, learning paths and certificates.
4. **Support/content operations:** scoped support and content review permissions.

## Feature scope

### Platform foundation
- Registration, login, password reset, email verification, profile and avatar.
- Role/permission management, policies, audit logs and notifications.
- Search, pagination, filtering and secure file uploads.

### Course catalogue and authoring
- Categories, tags, searchable catalogue and course detail pages.
- Draft/review/published/archived workflow; free and paid course support.
- Course → sections → lessons hierarchy.
- Lesson types: text, video URL, downloadable file, external link and embedded content.
- Resource attachments, preview lessons, sorting/reordering and publishing.

### Learning experience
- Free/paid enrollment, access checks and learner dashboard.
- Lesson completion, resume position, progress percentage and bookmarks.
- Learning paths that sequence multiple courses.
- Course reviews restricted to enrolled learners.

### Assessment and grading
- Reusable question banks.
- Single-choice, multiple-select, true/false and short-answer questions.
- Quiz time limits, attempt limits, pass percentage, question shuffle and result visibility.
- Attempt history, answer snapshots and objective auto-grading.
- Assignment instructions, due dates, text/file submissions, grading and feedback.

### Engagement
- Course announcements, discussion threads and replies.
- Notifications for enrollment, announcements, grading and certificates.
- Scheduled live-session links and attendance records (video is hosted by an external meeting provider).

### Credentials and analytics
- Completion rules, certificates with unique verification codes and public verification.
- Admin, instructor and learner dashboards.
- Enrollment, completion, assessment and course engagement reporting.

### Commerce (simulation only)
- Course prices, orders, order items, simulated payments and refunds.
- Use deterministic fake payment outcomes. Never collect/store real card numbers, CVV, UPI PINs or banking passwords. Do not claim to be a payment gateway.

## Stack and architecture

- Latest stable Laravel release compatible with installed PHP.
- PHP, MySQL 8+ or MariaDB 10.6+, Blade, Bootstrap 5.
- Use Laravel's recommended lightweight starter kit for authentication.
- REST API under `/api/v1`; Postman for testing.
- Pest or PHPUnit for tests.
- Build a server-rendered Blade application first. Do not introduce a separate React/Vue frontend in v1.

Use Laravel conventions: Eloquent models, migrations, seeders, Form Requests, policies, middleware, API Resources, notifications, events/listeners and queues. Keep controllers thin. Put multi-step business rules in focused Actions/Services. Use database transactions when multiple writes must succeed together.

## Suggested folder organization

```text
app/
  Actions/{Courses,Enrollments,Learning,Assessments,Assignments,Certificates,Commerce}/
  Enums/
  Http/Controllers/{Admin,Instructor,Learner,Api/V1}/
  Http/Requests/
  Models/
  Notifications/
  Policies/
  Services/{Payments,Reporting}/
database/{migrations,seeders,factories}/
resources/views/{layouts,admin,instructor,learner,courses,assessments}/
routes/{web.php,api.php}
tests/{Feature,Unit}/
```

Add folders when needed; do not create empty abstractions for appearance.

## Development roadmap

| Phase | Deliverable |
|---|---|
| 0 | Laravel project, Git, environment and DB connection |
| 1 | Authentication, profile, roles and permissions |
| 2 | Categories, course CRUD and instructor ownership |
| 3 | Sections, lessons, resources and publishing |
| 4 | Catalogue, enrollment and learner dashboard |
| 5 | Lesson progress, bookmarks and learning paths |
| 6 | Question bank, quizzes and attempts |
| 7 | Assignments, submissions and grading |
| 8 | Announcements, discussions and notifications |
| 9 | Certificates and public verification |
| 10 | Simulated orders, payments and refunds |
| 11 | Live sessions, attendance and reports |
| 12 | Tests, security review and deployment |

For every module: migration, model relationships, Form Request, policy, thin controller, UI states, tests, and README progress update.

## Database

- Database: **`learnsphere_db`**
- Initial schema: [`database/learnsphere_schema.sql`](database/learnsphere_schema.sql)
- Import notes: [`database/README.md`](database/README.md)
- SQL bootstrap contains 40 tables and starter role/permission records.
- Laravel migrations must become the authoritative schema history. Do not import the SQL snapshot and then blindly run duplicate create-table migrations.
- SQL is a portable bootstrap/reference snapshot; the script does not drop tables. Existing tables are not altered by `CREATE TABLE IF NOT EXISTS`.

Money: use decimal-safe arithmetic (never PHP floats); this starter uses `DECIMAL(10,2)` for course and payment amounts. For advanced payment accounting, prefer integer minor units. Store file paths, not file blobs. Hash passwords with Laravel `Hash`. Store timestamps in UTC where practical. Preserve quiz question/answer snapshots for historical attempts.

## Windows local setup

Prerequisites: compatible PHP, Composer, Git, MySQL/MariaDB; Node.js if required by the chosen starter kit.

```powershell
php -v
composer --version
git --version
composer create-project laravel/laravel learnsphere
cd learnsphere
```

Configure `.env`:

```dotenv
APP_NAME=LearnSphere
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://learnsphere.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learnsphere_db
DB_USERNAME=root
DB_PASSWORD=
```

Check the actual WAMP database port; use `3307` if that is your configured port, not `3306`.

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate:status
php artisan serve
```

**Choose one schema path:** preferred Laravel-first approach is an empty database plus Laravel migrations. If importing the SQL snapshot, baseline/adapt migrations first; duplicate create-table migrations will fail.

Git:

```powershell
git init
git branch -M main
git add .
git commit -m "chore: initialize LearnSphere Laravel project"
git checkout -b develop
```

Never commit `.env`, credentials, generated secrets or private learner uploads.

## Initial API plan

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/api/v1/courses` | Browse published courses |
| GET | `/api/v1/courses/{course}` | Course detail |
| POST | `/api/v1/courses/{course}/enroll` | Enroll current learner |
| GET | `/api/v1/learner/courses` | Learner's courses |
| POST | `/api/v1/lessons/{lesson}/complete` | Complete a lesson |
| GET | `/api/v1/quizzes/{quiz}` | Quiz detail, without answer keys |
| POST | `/api/v1/quizzes/{quiz}/attempts` | Start quiz attempt |
| POST | `/api/v1/quiz-attempts/{attempt}/submit` | Submit attempt |
| POST | `/api/v1/assignments/{assignment}/submissions` | Submit assignment |
| GET | `/api/v1/certificates/{code}/verify` | Verify certificate |

Use API Resources, Form Requests, auth middleware, policies, pagination and consistent errors. Never expose quiz answer keys before submission unless quiz settings explicitly allow it.

## Security checklist

- Enforce authorization server-side; hiding buttons is not authorization.
- Instructors may manage only courses they own or are explicitly assigned.
- Learners may access only enrolled course content.
- Validate upload MIME type, size and storage location.
- Prevent IDOR and mass-assignment vulnerabilities.
- Do not expose private learner data or quiz answers in API responses.
- Prevent duplicate enrollment, attempts where limits apply, and certificate issuance.
- Validate all payment/order state transitions.
- Rate-limit login and public verification endpoints.
- Keep CSRF protection enabled for web forms.
- Production: HTTPS, `APP_DEBUG=false`, backups and queue monitoring.

## Cursor AI working agreement

Paste this into Cursor Chat:

> You are helping me build LearnSphere using **Laravel and PHP**. This repository must remain Laravel. Do not use CodeIgniter controllers, models, routes, `php spark`, or CI-specific patterns.
>
> Before changing files, inspect the existing Laravel version, project structure, routes, models, migrations and tests. Do not assume files or columns exist. First explain the workflow, roles, tables, routes, validation and authorization plan. Work on one small feature at a time.
>
> Follow Laravel conventions: Eloquent relationships, migrations, seeders, Form Requests, policies, middleware, Blade, API Resources and automated tests. Keep controllers thin and place business rules in focused Actions/Services. Use transactions for related writes.
>
> Never invent schema. Inspect migrations first. Every schema change must be a Laravel migration; do not silently change only the SQL snapshot. Do not rewrite unrelated files, remove existing features or add packages without explaining why.
>
> Enforce authorization server-side, validate inputs and uploads, protect quiz answer keys, and never store plaintext passwords or real payment credentials. Never use PHP floating-point arithmetic for money.
>
> Explain each created/modified file, relationships, routes, security considerations and how I can test manually. Add feature/unit tests for important success, validation and unauthorized cases. I am learning Laravel, so explain why each important Laravel concept is used.

### Feature prompt template

```text
Implement the next LearnSphere feature: [FEATURE].

First inspect the existing Laravel project and schema. Do not edit files yet.
1. Explain user workflow and role permissions.
2. List models, tables and relationships.
3. List routes, validation and authorization.
4. Identify migrations required.
5. Propose a small implementation sequence.

Wait for approval before coding. Then implement only this feature, add tests,
and explain how I can verify it locally.
```

## Git branches

- `main` — stable milestones
- `develop` — integration
- `feature/auth-roles`
- `feature/course-management`
- `feature/course-content`
- `feature/enrollment-progress`
- `feature/quizzes`
- `feature/assignments`
- `feature/notifications`
- `feature/certificates`
- `feature/commerce`
- `feature/reports`

## Progress tracker

- [x] Phase 0 — Laravel setup and DB connection
- [x] Phase 1 — Authentication, roles and permissions
- [x] Phase 2 — Course catalogue and authoring
- [x] Phase 3 — Enrollment and progress
- [x] Phase 4 — Question bank and quizzes
- [x] Phase 5 — Assignments and grading
- [x] Phase 6 — Communication and notifications
- [x] Phase 7 — Certificates
- [x] Phase 8 — Simulated commerce
- [x] Phase 9 — Analytics and reports
- [x] Phase 10 — Tests, security and deployment

### Demo logins (password for all: `password`)

| Role | Email |
|---|---|
| Super Admin | `admin@learnsphere.test` |
| Instructor | `instructor@learnsphere.test` |
| Learner | `learner@learnsphere.test` |
| Content Manager | `content@learnsphere.test` |
| Support Staff | `support@learnsphere.test` |

Local run: configure `.env` DB (`learnsphere_db`, port `3307` on this WAMP), then `php artisan migrate --seed` and `php artisan serve`.
