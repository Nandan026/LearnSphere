<?php

declare(strict_types=1);

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\Content;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\Learner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Support;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/certificates/verify', [CertificateVerificationController::class, 'show'])->name('certificates.verify');
Route::post('/certificates/verify', [CertificateVerificationController::class, 'verify'])->name('certificates.verify.submit');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/catalogue', [Learner\CatalogueController::class, 'index'])->name('catalogue.index');
    Route::get('/catalogue/{course}', [Learner\CatalogueController::class, 'show'])->name('catalogue.show');
    Route::post('/catalogue/{course}/enroll', [Learner\EnrollmentController::class, 'store'])->name('catalogue.enroll');

    Route::prefix('learn')->name('learner.')->group(function (): void {
        Route::get('/certificates', [Learner\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [Learner\CertificateController::class, 'show'])->name('certificates.show');

        Route::get('/orders', [Learner\OrderController::class, 'index'])->name('orders.index');
        Route::get('/checkout/{course}', [Learner\OrderController::class, 'checkout'])->name('orders.checkout');
        Route::post('/checkout/{course}', [Learner\OrderController::class, 'place'])->name('orders.place');

        Route::get('/paths', [Learner\LearningPathController::class, 'index'])->name('paths.index');
        Route::get('/paths/{path}', [Learner\LearningPathController::class, 'show'])->name('paths.show');

        Route::get('/notifications', [Learner\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [Learner\NotificationController::class, 'markRead'])->name('notifications.read');

        Route::post('/quizzes/{quiz}/start', [Learner\QuizAttemptController::class, 'start'])->name('quizzes.start');
        Route::get('/quiz-attempts/{attempt}', [Learner\QuizAttemptController::class, 'take'])->name('quizzes.take');
        Route::post('/quiz-attempts/{attempt}/submit', [Learner\QuizAttemptController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quiz-attempts/{attempt}/result', [Learner\QuizAttemptController::class, 'result'])->name('quizzes.result');

        Route::get('/assignments/{assignment}/submit', [Learner\AssignmentSubmissionController::class, 'create'])->name('assignments.submit');
        Route::post('/assignments/{assignment}/submit', [Learner\AssignmentSubmissionController::class, 'store'])->name('assignments.store');

        Route::get('/courses/{course}/discussions', [Learner\DiscussionController::class, 'index'])->name('discussions.index');
        Route::post('/courses/{course}/discussions', [Learner\DiscussionController::class, 'storeThread'])->name('discussions.store');
        Route::post('/courses/{course}/discussions/{thread}/reply', [Learner\DiscussionController::class, 'reply'])->name('discussions.reply');

        Route::get('/{course}', [Learner\CoursePlayerController::class, 'show'])->whereNumber('course')->name('player.show');
        Route::get('/{course}/lessons/{lesson}', [Learner\CoursePlayerController::class, 'show'])->whereNumber('course')->name('player.lesson');
        Route::post('/{course}/lessons/{lesson}/complete', [Learner\CoursePlayerController::class, 'complete'])->whereNumber('course')->name('player.complete');
        Route::post('/{course}/lessons/{lesson}/bookmark', [Learner\CoursePlayerController::class, 'bookmark'])->whereNumber('course')->name('player.bookmark');
    });

    Route::middleware('role:instructor|super-admin')->prefix('instructor')->name('instructor.')->group(function (): void {
        Route::get('/courses', [Instructor\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [Instructor\CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [Instructor\CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}', [Instructor\CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [Instructor\CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [Instructor\CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [Instructor\CourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/submit-review', [Instructor\CourseController::class, 'submitForReview'])->name('courses.submit-review');
        Route::post('/courses/{course}/publish', [Instructor\CourseController::class, 'publish'])->name('courses.publish');
        Route::get('/courses/{course}/analytics', [Instructor\AnalyticsController::class, 'show'])->name('analytics.show');

        Route::post('/courses/{course}/sections', [Instructor\SectionController::class, 'store'])->name('sections.store');
        Route::put('/courses/{course}/sections/{section}', [Instructor\SectionController::class, 'update'])->name('sections.update');
        Route::delete('/courses/{course}/sections/{section}', [Instructor\SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/courses/{course}/sections/reorder', [Instructor\SectionController::class, 'reorder'])->name('sections.reorder');

        Route::post('/courses/{course}/sections/{section}/lessons', [Instructor\LessonController::class, 'store'])->name('lessons.store');
        Route::put('/courses/{course}/lessons/{lesson}', [Instructor\LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/courses/{course}/lessons/{lesson}', [Instructor\LessonController::class, 'destroy'])->name('lessons.destroy');

        Route::get('/courses/{course}/question-banks', [Instructor\QuestionBankController::class, 'index'])->name('question-banks.index');
        Route::post('/courses/{course}/question-banks', [Instructor\QuestionBankController::class, 'store'])->name('question-banks.store');
        Route::put('/courses/{course}/question-banks/{bank}', [Instructor\QuestionBankController::class, 'update'])->name('question-banks.update');
        Route::delete('/courses/{course}/question-banks/{bank}', [Instructor\QuestionBankController::class, 'destroy'])->name('question-banks.destroy');
        Route::post('/courses/{course}/question-banks/{bank}/questions', [Instructor\QuestionController::class, 'store'])->name('questions.store');
        Route::delete('/courses/{course}/questions/{question}', [Instructor\QuestionController::class, 'destroy'])->name('questions.destroy');

        Route::get('/courses/{course}/quizzes', [Instructor\QuizController::class, 'index'])->name('quizzes.index');
        Route::post('/courses/{course}/quizzes', [Instructor\QuizController::class, 'store'])->name('quizzes.store');
        Route::get('/courses/{course}/quizzes/{quiz}/edit', [Instructor\QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/courses/{course}/quizzes/{quiz}', [Instructor\QuizController::class, 'update'])->name('quizzes.update');
        Route::post('/courses/{course}/quizzes/{quiz}/questions', [Instructor\QuizController::class, 'attachQuestions'])->name('quizzes.attach');
        Route::delete('/courses/{course}/quizzes/{quiz}', [Instructor\QuizController::class, 'destroy'])->name('quizzes.destroy');

        Route::get('/courses/{course}/assignments', [Instructor\AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/courses/{course}/assignments', [Instructor\AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/courses/{course}/assignments/{assignment}/edit', [Instructor\AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/courses/{course}/assignments/{assignment}', [Instructor\AssignmentController::class, 'update'])->name('assignments.update');
        Route::get('/courses/{course}/assignments/{assignment}/submissions', [Instructor\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('/courses/{course}/submissions/{submission}/grade', [Instructor\AssignmentController::class, 'grade'])->name('assignments.grade');
        Route::delete('/courses/{course}/assignments/{assignment}', [Instructor\AssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::get('/courses/{course}/announcements', [Instructor\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/courses/{course}/announcements', [Instructor\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/courses/{course}/announcements/{announcement}', [Instructor\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        Route::get('/courses/{course}/live-sessions', [Instructor\LiveSessionController::class, 'index'])->name('live-sessions.index');
        Route::post('/courses/{course}/live-sessions', [Instructor\LiveSessionController::class, 'store'])->name('live-sessions.store');
        Route::delete('/courses/{course}/live-sessions/{session}', [Instructor\LiveSessionController::class, 'destroy'])->name('live-sessions.destroy');

        Route::get('/discussions', [Instructor\DiscussionModerationController::class, 'index'])->name('discussions.index');
    });

    Route::middleware('role:super-admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::resource('users', Admin\UserController::class)->except(['show']);
        Route::resource('categories', Admin\CategoryController::class)->except(['show']);
        Route::get('/moderation', [Admin\CourseModerationController::class, 'index'])->name('moderation.index');
        Route::post('/moderation/{course}/publish', [Admin\CourseModerationController::class, 'publish'])->name('moderation.publish');
        Route::post('/moderation/{course}/archive', [Admin\CourseModerationController::class, 'archive'])->name('moderation.archive');
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/audit', [Admin\AuditLogController::class, 'index'])->name('audit.index');
    });

    Route::middleware('role:content-manager')->prefix('content')->name('content.')->group(function (): void {
        Route::get('/moderation', [Content\CourseModerationController::class, 'index'])->name('moderation.index');
        Route::post('/moderation/{course}/publish', [Content\CourseModerationController::class, 'publish'])->name('moderation.publish');
    });

    Route::middleware('role:support-staff')->prefix('support')->name('support.')->group(function (): void {
        Route::get('/users', [Support\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [Support\UserController::class, 'show'])->name('users.show');
        Route::get('/enrollments', [Support\EnrollmentController::class, 'index'])->name('enrollments.index');
    });
});
