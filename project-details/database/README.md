# LearnSphere database

Database: `learnsphere_db`  
Engine: InnoDB · Charset: utf8mb4 · Target: MySQL 8+ / MariaDB 10.6+

Import `learnsphere_schema.sql` using Navicat/phpMyAdmin or the MySQL client. It creates the database and 40 tables. It has no DROP statements. `CREATE TABLE IF NOT EXISTS` does not alter an existing table; use Laravel migrations for changes.

Laravel `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learnsphere_db
DB_USERNAME=root
DB_PASSWORD=
```

Use `3307` if that is your actual WAMP database port.

**Important:** Recommended Laravel-first setup is an empty database plus Laravel migrations. The SQL file is a portable initial snapshot/reference. If you import it, do not blindly run migrations that create the same tables. Baseline/adapt migrations first.

Tables:
- Identity: users, roles, permissions, user_roles, role_permissions
- Catalogue: course_categories, courses, tags, course_tags
- Authoring: course_sections, lessons, lesson_resources
- Learning: enrollments, lesson_progress, bookmarks, course_reviews, learning_paths, learning_path_courses
- Assessments: question_banks, questions, question_options, quizzes, quiz_questions, quiz_attempts, quiz_answers
- Assignments: assignments, assignment_submissions, assignment_feedback
- Communication: announcements, discussion_threads, discussion_replies, notifications
- Credentials: certificates
- Simulated commerce: orders, order_items, payments, refunds
- Live learning: live_sessions, live_session_attendance
- Operations: audit_logs

Prices and payments use DECIMAL(10,2); use decimal-safe arithmetic, never PHP floats. Files live on Laravel storage disks; SQL stores paths and metadata. Passwords must be hashed by Laravel. Payment records are simulated only.
