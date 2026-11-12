<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $quizzes = $course->quizzes()->with('section')->latest()->get();

        return view('instructor.quizzes.index', compact('course', 'quizzes'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'section_id' => ['nullable', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'pass_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $course->quizzes()->create([
            ...$data,
            'shuffle_questions' => false,
            'show_results' => true,
            'status' => 'draft',
        ]);

        return back()->with('success', 'Quiz created.');
    }

    public function edit(Course $course, Quiz $quiz): View
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);
        $quiz->load('questions');
        $banks = $course->id ? QuestionBank::query()->where('course_id', $course->id)->with('questions')->get() : collect();

        return view('instructor.quizzes.edit', compact('course', 'quiz', 'banks'));
    }

    public function update(Request $request, Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'pass_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);

        $quiz->update($data);

        return back()->with('success', 'Quiz updated.');
    }

    public function attachQuestions(Request $request, Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);

        $data = $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
        ]);

        $sync = [];
        foreach ($data['question_ids'] as $index => $questionId) {
            $sync[$questionId] = ['marks' => 1, 'sort_order' => $index + 1];
        }

        $quiz->questions()->syncWithoutDetaching($sync);

        return back()->with('success', 'Questions attached.');
    }

    public function destroy(Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($quiz->course_id === $course->id, 404);
        $quiz->delete();

        return back()->with('success', 'Quiz deleted.');
    }
}
