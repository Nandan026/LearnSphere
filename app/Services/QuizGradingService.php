<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class QuizGradingService
{
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        $inProgress = QuizAttempt::query()
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress !== null) {
            return $inProgress;
        }

        $attemptCount = QuizAttempt::query()
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->count();

        if ($quiz->max_attempts !== null && $attemptCount >= $quiz->max_attempts) {
            throw ValidationException::withMessages([
                'quiz' => ['Maximum number of attempts has been reached for this quiz.'],
            ]);
        }

        $nextAttemptNumber = (int) QuizAttempt::query()
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->max('attempt_number') + 1;

        return QuizAttempt::query()->create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'attempt_number' => $nextAttemptNumber,
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);
    }

    /**
     * @param  array<int, mixed>  $answers
     */
    public function submitAttempt(QuizAttempt $attempt, array $answers): QuizAttempt
    {
        $attempt->loadMissing('quiz');

        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();

        $totalScore = 0.0;
        $maxScore = 0.0;

        foreach ($questions as $question) {
            $marks = (float) ($question->pivot->marks ?? $question->default_marks);
            $maxScore += $marks;

            $answerValue = $answers[$question->id] ?? null;
            $graded = $this->gradeQuestion($question, $answerValue, $marks);

            QuizAnswer::query()->updateOrCreate(
                [
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_snapshot' => $this->buildQuestionSnapshot($question, $marks),
                    'answer_payload' => $this->normalizeAnswerPayload($answerValue),
                    'is_correct' => $graded['is_correct'],
                    'marks_awarded' => $graded['marks_awarded'],
                ],
            );

            $totalScore += $graded['marks_awarded'];
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0.0;
        $passed = $percentage >= (float) $quiz->pass_percentage;

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
            'score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $percentage,
            'passed' => $passed,
        ]);

        return $attempt->fresh();
    }

    /**
     * @return array{is_correct: bool|null, marks_awarded: float}
     */
    private function gradeQuestion(Question $question, mixed $answerValue, float $marks): array
    {
        return match ($question->question_type) {
            'single_choice', 'true_false' => $this->gradeSingleChoice($question, $answerValue, $marks),
            'multiple_select' => $this->gradeMultipleSelect($question, $answerValue, $marks),
            'short_answer' => ['is_correct' => null, 'marks_awarded' => 0.0],
            default => ['is_correct' => false, 'marks_awarded' => 0.0],
        };
    }

    /**
     * @return array{is_correct: bool|null, marks_awarded: float}
     */
    private function gradeSingleChoice(Question $question, mixed $answerValue, float $marks): array
    {
        $selectedId = $this->extractOptionId($answerValue);

        if ($selectedId === null) {
            return ['is_correct' => false, 'marks_awarded' => 0.0];
        }

        $option = $question->options->firstWhere('id', $selectedId);
        $isCorrect = $option !== null && $option->is_correct;

        return [
            'is_correct' => $isCorrect,
            'marks_awarded' => $isCorrect ? $marks : 0.0,
        ];
    }

    /**
     * @return array{is_correct: bool|null, marks_awarded: float}
     */
    private function gradeMultipleSelect(Question $question, mixed $answerValue, float $marks): array
    {
        $selectedIds = collect($this->extractOptionIds($answerValue))->map(fn ($id) => (int) $id)->sort()->values();
        $correctIds = $question->options->where('is_correct', true)->pluck('id')->sort()->values();

        if ($selectedIds->isEmpty()) {
            return ['is_correct' => false, 'marks_awarded' => 0.0];
        }

        $isCorrect = $selectedIds->all() === $correctIds->all();

        return [
            'is_correct' => $isCorrect,
            'marks_awarded' => $isCorrect ? $marks : 0.0,
        ];
    }

    private function extractOptionId(mixed $answerValue): ?int
    {
        if (is_int($answerValue) || (is_string($answerValue) && ctype_digit($answerValue))) {
            return (int) $answerValue;
        }

        if (is_array($answerValue)) {
            if (isset($answerValue['option_id'])) {
                return (int) $answerValue['option_id'];
            }

            if (isset($answerValue[0])) {
                return (int) $answerValue[0];
            }
        }

        return null;
    }

    /**
     * @return list<int>
     */
    private function extractOptionIds(mixed $answerValue): array
    {
        if (is_array($answerValue)) {
            if (isset($answerValue['option_ids']) && is_array($answerValue['option_ids'])) {
                return array_map('intval', $answerValue['option_ids']);
            }

            return array_map('intval', array_values($answerValue));
        }

        if ($answerValue === null) {
            return [];
        }

        return [(int) $answerValue];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildQuestionSnapshot(Question $question, float $marks): array
    {
        return [
            'id' => $question->id,
            'question_type' => $question->question_type,
            'question_text' => $question->question_text,
            'marks' => $marks,
            'options' => $question->options->map(fn ($option) => [
                'id' => $option->id,
                'option_text' => $option->option_text,
                'is_correct' => (bool) $option->is_correct,
                'sort_order' => $option->sort_order,
            ])->values()->all(),
        ];
    }

    private function normalizeAnswerPayload(mixed $answerValue): ?array
    {
        if ($answerValue === null) {
            return null;
        }

        if (is_array($answerValue)) {
            return $answerValue;
        }

        if (is_bool($answerValue)) {
            return ['value' => $answerValue];
        }

        if (is_scalar($answerValue)) {
            return ['value' => $answerValue];
        }

        return null;
    }
}
