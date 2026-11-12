<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LiveSessionController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $sessions = $course->liveSessions()->latest('starts_at')->get();

        return view('instructor.live-sessions.index', compact('course', 'sessions'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_url' => ['required', 'url'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        $course->liveSessions()->create([
            ...$data,
            'created_by' => Auth::id(),
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Live session scheduled.');
    }

    public function destroy(Course $course, LiveSession $session): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($session->course_id === $course->id, 404);
        $session->delete();

        return back()->with('success', 'Session removed.');
    }
}
