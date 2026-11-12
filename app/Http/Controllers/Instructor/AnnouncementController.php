<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $announcements = $course->announcements()->latest()->get();

        return view('instructor.announcements.index', compact('course', 'announcements'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $course->announcements()->create([
            'created_by' => Auth::id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'publish_at' => now(),
            'status' => 'published',
        ]);

        return back()->with('success', 'Announcement posted.');
    }

    public function destroy(Course $course, Announcement $announcement): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($announcement->course_id === $course->id, 404);
        $announcement->delete();

        return back()->with('success', 'Announcement removed.');
    }
}
