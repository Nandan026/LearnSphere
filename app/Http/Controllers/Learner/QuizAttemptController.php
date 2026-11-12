<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuizAttemptController extends Controller
{
    public function __construct(
        private readonly QuizGradingService $quizGradingService,
    ) {}

    public function start(Quiz $quiz): RedirectResponse
    {
        $this->assertEnrolled($quiz);
        $attempt = $this->quizGradingService->startAttempt(Auth::user(), $quiz);

        return redirect()->route('learner.quizzes.take', $attempt);
    }

    public function take(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        $attempt->load(['quiz.questions.options']);

        return view('learner.quizzes.take', compact('attempt'));
    }

    public function submit(Request $request, QuizAttempt $attempt): RedirectResponse
    {
        abort_unless($attempt->user_id === Auth::id(), 403);

        $answers = $request->input('answers', []);
        $graded = $this->quizGradingService->submitAttempt($attempt, is_array($answers) ? $answers : []);

        return redirect()->route('learner.quizzes.result', $graded);
    }

    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === Auth::id(), 403);
        $attempt->load(['quiz', 'answers']);

        return view('learner.quizzes.result', compact('attempt'));
    }

    private function assertEnrolled(Quiz $quiz): void
    {
        Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $quiz->course_id)
            ->where('status', 'active')
            ->firstOrFail();
    }
}
