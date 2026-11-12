<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizGradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizGradingService $quizGradingService,
    ) {}

    public function show(Quiz $quiz): JsonResponse
    {
        abort_unless($quiz->status === 'published', 404);

        $quiz->load(['questions' => fn ($q) => $q->with(['options' => fn ($oq) => $oq->select('id', 'question_id', 'option_text', 'sort_order')])]);

        return response()->json($quiz);
    }

    public function startAttempt(Quiz $quiz): JsonResponse
    {
        Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $quiz->course_id)
            ->firstOrFail();

        $attempt = $this->quizGradingService->startAttempt(Auth::user(), $quiz);

        return response()->json($attempt, 201);
    }

    public function submitAttempt(Request $request, QuizAttempt $attempt): JsonResponse
    {
        abort_unless($attempt->user_id === Auth::id(), 403);

        $answers = $request->input('answers', []);
        $graded = $this->quizGradingService->submitAttempt($attempt, is_array($answers) ? $answers : []);

        return response()->json($graded);
    }
}
