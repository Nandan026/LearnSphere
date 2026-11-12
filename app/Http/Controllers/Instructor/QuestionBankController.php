<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $banks = $course->questionBanks()->withCount('questions')->get();

        return view('instructor.question-banks.index', compact('course', 'banks'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        QuestionBank::query()->create([
            'course_id' => $course->id,
            'created_by' => Auth::id(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', 'Question bank created.');
    }

    public function update(Request $request, Course $course, QuestionBank $bank): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($bank->course_id === $course->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $bank->update($data);

        return back()->with('success', 'Question bank updated.');
    }

    public function destroy(Course $course, QuestionBank $bank): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($bank->course_id === $course->id, 404);
        $bank->delete();

        return back()->with('success', 'Question bank deleted.');
    }
}
