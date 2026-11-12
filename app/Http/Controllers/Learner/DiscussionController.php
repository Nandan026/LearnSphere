<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DiscussionReply;
use App\Models\DiscussionThread;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscussionController extends Controller
{
    public function index(Course $course): View
    {
        $this->assertEnrolled($course);
        $threads = $course->discussionThreads()->with(['user', 'replies.user'])->latest()->get();

        return view('learner.discussions.index', compact('course', 'threads'));
    }

    public function storeThread(Request $request, Course $course): RedirectResponse
    {
        $this->assertEnrolled($course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $course->discussionThreads()->create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Thread created.');
    }

    public function reply(Request $request, Course $course, DiscussionThread $thread): RedirectResponse
    {
        $this->assertEnrolled($course);
        abort_unless($thread->course_id === $course->id, 404);

        $data = $request->validate(['body' => ['required', 'string']]);

        DiscussionReply::query()->create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Reply posted.');
    }

    private function assertEnrolled(Course $course): void
    {
        Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();
    }
}
