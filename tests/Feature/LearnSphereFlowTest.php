<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\SimulatedPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnSphereFlowTest extends TestCase
{
    /**
     * Uses the imported SQL schema + seeded demo data (not RefreshDatabase).
     */
    public function test_demo_users_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'learner@learnsphere.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_page_shows_sample_credentials(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('admin@learnsphere.test')
            ->assertSee('password');
    }

    public function test_learner_can_complete_lesson_and_take_quiz(): void
    {
        $learner = User::where('email', 'learner@learnsphere.test')->firstOrFail();
        $course = Course::where('status', 'published')->where('price', 0)->firstOrFail();
        $lesson = Lesson::where('course_id', $course->id)->where('is_published', true)->firstOrFail();
        $quiz = Quiz::where('course_id', $course->id)->where('status', 'published')->firstOrFail();

        Enrollment::query()->firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $learner->id],
            ['enrollment_type' => 'free', 'status' => 'active', 'enrolled_at' => now()]
        );

        $this->actingAs($learner)
            ->post(route('learner.player.complete', [$course, $lesson]))
            ->assertRedirect();

        $start = $this->actingAs($learner)
            ->post(route('learner.quizzes.start', $quiz));

        $start->assertRedirect();
        $attemptId = (int) basename(parse_url($start->headers->get('Location'), PHP_URL_PATH));
        $this->assertGreaterThan(0, $attemptId);

        $quiz->load('questions.options');
        $answers = [];
        foreach ($quiz->questions as $question) {
            if (in_array($question->question_type, ['single_choice', 'true_false'], true)) {
                $correct = $question->options->firstWhere('is_correct', true);
                $answers[$question->id] = $correct?->id;
            } elseif ($question->question_type === 'multiple_select') {
                $answers[$question->id] = $question->options->where('is_correct', true)->pluck('id')->all();
            } else {
                $answers[$question->id] = 'sample answer';
            }
        }

        $this->actingAs($learner)
            ->post(route('learner.quizzes.submit', $attemptId), ['answers' => $answers])
            ->assertRedirect();
    }

    public function test_simulated_checkout_enrolls_learner(): void
    {
        $learner = User::where('email', 'learner@learnsphere.test')->firstOrFail();
        $course = Course::where('status', 'published')->where('price', '>', 0)->firstOrFail();

        Enrollment::query()->where('course_id', $course->id)->where('user_id', $learner->id)->delete();

        $order = app(SimulatedPaymentService::class)->checkout($learner, $course);

        $this->assertSame('paid', $order->status);
        $this->assertTrue(
            Enrollment::query()->where('course_id', $course->id)->where('user_id', $learner->id)->exists()
        );
    }

    public function test_certificate_issues_when_course_completed(): void
    {
        $learner = User::where('email', 'learner@learnsphere.test')->firstOrFail();
        $course = Course::where('status', 'published')->where('price', 0)->firstOrFail();
        $enrollment = Enrollment::query()->firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $learner->id],
            ['enrollment_type' => 'free', 'status' => 'active', 'enrolled_at' => now()]
        );
        $enrollment->update(['completed_at' => now()]);

        $certificate = app(CertificateService::class)->issueIfEligible($enrollment->fresh());

        $this->assertNotNull($certificate);
        $this->assertStringStartsWith('LS-', $certificate->certificate_code);
    }

    public function test_admin_users_index_ok(): void
    {
        $admin = User::where('email', 'admin@learnsphere.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Users guide');
    }

    public function test_instructor_cannot_access_admin(): void
    {
        $instructor = User::where('email', 'instructor@learnsphere.test')->firstOrFail();

        $this->actingAs($instructor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
