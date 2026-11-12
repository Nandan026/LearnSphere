<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bootstraps domain tables from the portable SQL snapshot.
     * Safe with CREATE TABLE IF NOT EXISTS. Skips users (created earlier).
     */
    public function up(): void
    {
        $path = base_path('project-details/database/learnsphere_schema.sql');
        if (! is_file($path)) {
            return;
        }

        $sql = file_get_contents($path);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($statements as $statement) {
            if ($statement === '' || str_starts_with($statement, '--')) {
                continue;
            }
            if (preg_match('/^(CREATE DATABASE|USE)\b/i', $statement)) {
                continue;
            }
            // users already created by the Laravel auth migration
            if (preg_match('/^CREATE TABLE IF NOT EXISTS `users`\b/i', $statement)) {
                continue;
            }
            DB::unprepared($statement);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        $tables = [
            'audit_logs', 'live_session_attendance', 'live_sessions', 'refunds', 'payments', 'order_items', 'orders',
            'certificates', 'notifications', 'discussion_replies', 'discussion_threads', 'announcements',
            'assignment_feedback', 'assignment_submissions', 'assignments', 'quiz_answers', 'quiz_attempts',
            'quiz_questions', 'quizzes', 'question_options', 'questions', 'question_banks', 'learning_path_courses',
            'learning_paths', 'course_reviews', 'bookmarks', 'lesson_progress', 'enrollments', 'lesson_resources',
            'lessons', 'course_sections', 'course_tags', 'tags', 'courses', 'course_categories', 'role_permissions',
            'user_roles', 'permissions', 'roles',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
