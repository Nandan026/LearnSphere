<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentFeedback;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $assignments = $course->assignments()->latest()->get();

        return view('instructor.assignments.index', compact('course', 'assignments'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'section_id' => ['nullable', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'max_marks' => ['required', 'numeric', 'min:1'],
            'due_at' => ['nullable', 'date'],
        ]);

        $course->assignments()->create([
            ...$data,
            'created_by' => Auth::id(),
            'allow_late_submission' => $request->boolean('allow_late_submission', true),
            'status' => 'published',
        ]);

        return back()->with('success', 'Assignment created.');
    }

    public function edit(Course $course, Assignment $assignment): View
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($assignment->course_id === $course->id, 404);

        return view('instructor.assignments.edit', compact('course', 'assignment'));
    }

    public function update(Request $request, Course $course, Assignment $assignment): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($assignment->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'max_marks' => ['required', 'numeric', 'min:1'],
            'due_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,published,closed'],
        ]);

        $assignment->update($data);

        return back()->with('success', 'Assignment updated.');
    }

    public function submissions(Course $course, Assignment $assignment): View
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($assignment->course_id === $course->id, 404);

        $submissions = $assignment->submissions()->with(['user', 'feedback'])->latest('submitted_at')->get();

        return view('instructor.assignments.submissions', compact('course', 'assignment', 'submissions'));
    }

    public function grade(Request $request, Course $course, AssignmentSubmission $submission): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($submission->assignment?->course_id === $course->id, 404);

        $data = $request->validate([
            'marks' => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string'],
        ]);

        $submission->update(['status' => 'graded', 'graded_at' => now()]);

        AssignmentFeedback::query()->updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => Auth::id(),
                'marks_awarded' => $data['marks'],
                'feedback' => $data['feedback'] ?? null,
            ],
        );

        return back()->with('success', 'Submission graded.');
    }

    public function destroy(Course $course, Assignment $assignment): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($assignment->course_id === $course->id, 404);
        $assignment->delete();

        return back()->with('success', 'Assignment deleted.');
    }
}
