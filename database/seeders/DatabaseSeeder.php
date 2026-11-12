<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSection;
use App\Models\DiscussionThread;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LiveSession;
use App\Models\Permission;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->ensureSuperAdminPermissions();

        $password = Hash::make('password');

        $admin = $this->seedUser('Super Admin', 'admin@learnsphere.test', $password, 'super-admin');
        $instructor = $this->seedUser('Instructor', 'instructor@learnsphere.test', $password, 'instructor');
        $learner = $this->seedUser('Learner', 'learner@learnsphere.test', $password, 'learner');
        $contentManager = $this->seedUser('Content Manager', 'content@learnsphere.test', $password, 'content-manager');
        $support = $this->seedUser('Support Staff', 'support@learnsphere.test', $password, 'support-staff');

        $categories = collect([
            ['name' => 'Web Development', 'slug' => 'web-development'],
            ['name' => 'Data Science', 'slug' => 'data-science'],
            ['name' => 'Business Skills', 'slug' => 'business-skills'],
        ])->map(fn (array $data, int $index) => CourseCategory::query()->updateOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'description' => "LearnSphere demo category: {$data['name']}.",
                'sort_order' => $index + 1,
                'status' => 'active',
            ],
        ));

        $tags = collect([
            ['name' => 'Beginner Friendly', 'slug' => 'beginner-friendly'],
            ['name' => 'Certificate', 'slug' => 'certificate'],
        ])->map(fn (array $data) => Tag::query()->updateOrCreate(
            ['slug' => $data['slug']],
            ['name' => $data['name']],
        ));

        $freeCourse = Course::query()->updateOrCreate(
            ['slug' => 'intro-to-laravel'],
            [
                'category_id' => $categories[0]->id,
                'instructor_id' => $instructor->id,
                'title' => 'Introduction to Laravel',
                'short_description' => 'Build your first Laravel applications with hands-on lessons.',
                'description' => '<p>A free introductory course covering routing, controllers, Eloquent, and Blade.</p>',
                'level' => 'beginner',
                'language' => 'en',
                'price' => 0,
                'currency' => 'INR',
                'estimated_minutes' => 180,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
            ],
        );

        $paidCourse = Course::query()->updateOrCreate(
            ['slug' => 'advanced-php-patterns'],
            [
                'category_id' => $categories[1]->id,
                'instructor_id' => $instructor->id,
                'title' => 'Advanced PHP Patterns',
                'short_description' => 'Deep dive into modern PHP architecture and design patterns.',
                'description' => '<p>Paid course covering SOLID, repositories, events, and service layers.</p>',
                'level' => 'intermediate',
                'language' => 'en',
                'price' => 499.00,
                'currency' => 'INR',
                'estimated_minutes' => 360,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
            ],
        );

        $freeCourse->tags()->syncWithoutDetaching($tags->pluck('id'));
        $paidCourse->tags()->syncWithoutDetaching($tags->pluck('id'));

        $freeSection = CourseSection::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Getting Started'],
            [
                'description' => 'Course overview and environment setup.',
                'sort_order' => 1,
                'is_published' => true,
            ],
        );

        $paidSection = CourseSection::query()->updateOrCreate(
            ['course_id' => $paidCourse->id, 'title' => 'Core Patterns'],
            [
                'description' => 'Foundational design patterns in PHP.',
                'sort_order' => 1,
                'is_published' => true,
            ],
        );

        $textLesson = Lesson::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'slug' => 'welcome'],
            [
                'section_id' => $freeSection->id,
                'title' => 'Welcome & Course Outline',
                'lesson_type' => 'text',
                'content' => '<p>Welcome to LearnSphere. This lesson introduces the LMS workflow.</p>',
                'sort_order' => 1,
                'is_preview' => true,
                'is_published' => true,
            ],
        );

        $videoLesson = Lesson::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'slug' => 'installing-laravel'],
            [
                'section_id' => $freeSection->id,
                'title' => 'Installing Laravel',
                'lesson_type' => 'video',
                'video_url' => 'https://example.com/videos/laravel-install.mp4',
                'duration_seconds' => 600,
                'sort_order' => 2,
                'is_published' => true,
            ],
        );

        Lesson::query()->updateOrCreate(
            ['course_id' => $paidCourse->id, 'slug' => 'repository-pattern'],
            [
                'section_id' => $paidSection->id,
                'title' => 'Repository Pattern',
                'lesson_type' => 'text',
                'content' => '<p>Separating persistence from business logic using repositories.</p>',
                'sort_order' => 1,
                'is_published' => true,
            ],
        );

        $questionBank = QuestionBank::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'name' => 'Laravel Basics Bank'],
            [
                'created_by' => $instructor->id,
                'description' => 'Demo questions for the introductory quiz.',
            ],
        );

        $singleChoice = $this->seedQuestion(
            $questionBank,
            'single_choice',
            'Which Artisan command starts the development server?',
            [
                ['text' => 'php artisan serve', 'correct' => true],
                ['text' => 'php artisan start', 'correct' => false],
                ['text' => 'php artisan run', 'correct' => false],
            ],
        );

        $multipleSelect = $this->seedQuestion(
            $questionBank,
            'multiple_select',
            'Which of the following are Eloquent relationship types?',
            [
                ['text' => 'hasMany', 'correct' => true],
                ['text' => 'belongsTo', 'correct' => true],
                ['text' => 'rendersTo', 'correct' => false],
                ['text' => 'morphMany', 'correct' => true],
            ],
        );

        $trueFalse = $this->seedQuestion(
            $questionBank,
            'true_false',
            'Blade templates are compiled to plain PHP.',
            [
                ['text' => 'True', 'correct' => true],
                ['text' => 'False', 'correct' => false],
            ],
        );

        $shortAnswer = Question::query()->updateOrCreate(
            [
                'question_bank_id' => $questionBank->id,
                'question_text' => 'What does MVC stand for?',
            ],
            [
                'question_type' => 'short_answer',
                'explanation' => 'Model-View-Controller architectural pattern.',
                'default_marks' => 2,
                'difficulty' => 'easy',
                'status' => 'active',
            ],
        );

        $quiz = Quiz::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Laravel Basics Quiz'],
            [
                'section_id' => $freeSection->id,
                'description' => 'Check your understanding of Laravel fundamentals.',
                'max_attempts' => 3,
                'pass_percentage' => 60,
                'shuffle_questions' => false,
                'show_results' => true,
                'status' => 'published',
            ],
        );

        $quiz->questions()->sync([
            $singleChoice->id => ['marks' => 1, 'sort_order' => 1],
            $multipleSelect->id => ['marks' => 2, 'sort_order' => 2],
            $trueFalse->id => ['marks' => 1, 'sort_order' => 3],
            $shortAnswer->id => ['marks' => 2, 'sort_order' => 4],
        ]);

        Assignment::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Build a Hello World Route'],
            [
                'section_id' => $freeSection->id,
                'created_by' => $instructor->id,
                'instructions' => 'Create a web route that returns a personalized greeting.',
                'max_marks' => 100,
                'due_at' => Carbon::now()->addWeeks(2),
                'allow_late_submission' => true,
                'status' => 'published',
            ],
        );

        Announcement::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Welcome to the cohort'],
            [
                'created_by' => $instructor->id,
                'body' => 'Live office hours are every Friday at 5 PM IST.',
                'publish_at' => Carbon::now()->subDay(),
                'status' => 'published',
            ],
        );

        DiscussionThread::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Introduce yourself'],
            [
                'user_id' => $learner->id,
                'body' => 'Share your background and what you hope to learn in this course.',
                'status' => 'open',
            ],
        );

        $learningPath = LearningPath::query()->updateOrCreate(
            ['slug' => 'full-stack-php-track'],
            [
                'created_by' => $contentManager->id,
                'title' => 'Full-Stack PHP Track',
                'description' => 'From Laravel basics to advanced PHP architecture.',
                'status' => 'published',
            ],
        );

        $learningPath->courses()->sync([
            $freeCourse->id => ['sort_order' => 1],
            $paidCourse->id => ['sort_order' => 2],
        ]);

        LiveSession::query()->updateOrCreate(
            ['course_id' => $freeCourse->id, 'title' => 'Laravel Q&A Live'],
            [
                'created_by' => $instructor->id,
                'description' => 'Open Q&A for enrolled learners.',
                'meeting_url' => 'https://meet.example.com/learnsphere-laravel-qa',
                'starts_at' => Carbon::now()->addDays(3)->setTime(17, 0),
                'ends_at' => Carbon::now()->addDays(3)->setTime(18, 0),
                'status' => 'scheduled',
            ],
        );

        /** @var EnrollmentService $enrollmentService */
        $enrollmentService = app(EnrollmentService::class);
        $enrollment = $enrollmentService->enroll($learner, $freeCourse, 'free');

        $now = Carbon::now();
        LessonProgress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $textLesson->id,
            ],
            [
                'status' => 'completed',
                'started_at' => $now->copy()->subHour(),
                'completed_at' => $now,
            ],
        );

        LessonProgress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $videoLesson->id,
            ],
            [
                'status' => 'in_progress',
                'started_at' => $now,
                'last_position_seconds' => 120,
            ],
        );

        unset($admin, $support, $paidSection, $quiz, $singleChoice, $multipleSelect, $trueFalse, $shortAnswer);
    }

    private function ensureSuperAdminPermissions(): void
    {
        $superAdmin = Role::query()->where('slug', 'super-admin')->first();

        if ($superAdmin === null) {
            return;
        }

        $permissionIds = Permission::query()->pluck('id');
        $assignedCount = $superAdmin->permissions()->count();

        if ($assignedCount < $permissionIds->count()) {
            $superAdmin->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    private function seedUser(string $name, string $email, string $password, string $roleSlug): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'email_verified_at' => Carbon::now(),
                'status' => 'active',
            ],
        );

        $user->assignRole($roleSlug);

        return $user;
    }

    /**
     * @param  list<array{text: string, correct: bool}>  $options
     */
    private function seedQuestion(
        QuestionBank $bank,
        string $type,
        string $text,
        array $options,
    ): Question {
        $question = Question::query()->updateOrCreate(
            [
                'question_bank_id' => $bank->id,
                'question_text' => $text,
            ],
            [
                'question_type' => $type,
                'default_marks' => 1,
                'difficulty' => 'medium',
                'status' => 'active',
            ],
        );

        foreach ($options as $index => $option) {
            QuestionOption::query()->updateOrCreate(
                [
                    'question_id' => $question->id,
                    'option_text' => $option['text'],
                ],
                [
                    'is_correct' => $option['correct'],
                    'sort_order' => $index + 1,
                ],
            );
        }

        return $question;
    }
}
