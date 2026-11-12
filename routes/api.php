<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

/*
| API v1 — session cookie auth for same-origin requests (Sanctum not installed).
| Public GET endpoints below; POST routes require auth middleware (web session).
*/

Route::get('/health', fn () => ['status' => 'ok']);

Route::prefix('v1')->group(function (): void {
    Route::get('/courses', [V1\CourseController::class, 'index']);
    Route::get('/courses/{course}', [V1\CourseController::class, 'show']);
    Route::get('/certificates/{code}/verify', [V1\CertificateController::class, 'verify']);

    Route::middleware('auth')->group(function (): void {
        Route::post('/courses/{course}/enroll', [V1\EnrollmentController::class, 'store']);
        Route::get('/learner/courses', [V1\EnrollmentController::class, 'learnerCourses']);
        Route::post('/lessons/{lesson}/complete', [V1\LessonController::class, 'complete']);
        Route::get('/quizzes/{quiz}', [V1\QuizController::class, 'show']);
        Route::post('/quizzes/{quiz}/attempts', [V1\QuizController::class, 'startAttempt']);
        Route::post('/quiz-attempts/{attempt}/submit', [V1\QuizController::class, 'submitAttempt']);
        Route::post('/assignments/{assignment}/submissions', [V1\AssignmentController::class, 'submit']);
    });
});
