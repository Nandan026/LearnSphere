<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function store(Request $request, Course $course, QuestionBank $bank): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($bank->course_id === $course->id, 404);

        $data = $request->validate([
            'question_type' => ['required', Rule::in(['single_choice', 'multiple_select', 'true_false', 'short_answer'])],
            'question_text' => ['required', 'string'],
            'default_marks' => ['nullable', 'numeric', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.text' => ['required_with:options', 'string'],
            'options.*.correct' => ['nullable', 'boolean'],
        ]);

        $question = $bank->questions()->create([
            'question_type' => $data['question_type'],
            'question_text' => $data['question_text'],
            'default_marks' => $data['default_marks'] ?? 1,
            'difficulty' => 'medium',
            'status' => 'active',
        ]);

        foreach ($data['options'] ?? [] as $index => $option) {
            QuestionOption::query()->create([
                'question_id' => $question->id,
                'option_text' => $option['text'],
                'is_correct' => (bool) ($option['correct'] ?? false),
                'sort_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Question added.');
    }

    public function destroy(Course $course, Question $question): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($question->questionBank?->course_id === $course->id, 404);
        $question->delete();

        return back()->with('success', 'Question removed.');
    }
}
