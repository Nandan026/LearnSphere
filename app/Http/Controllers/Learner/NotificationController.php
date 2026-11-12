<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AssignmentFeedback;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $readIds = collect($request->session()->get('read_notifications', []));

        $enrolledCourseIds = Enrollment::query()->where('user_id', $user->id)->pluck('course_id');

        $items = collect();

        Announcement::query()
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'published')
            ->latest('publish_at')
            ->take(10)
            ->get()
            ->each(function (Announcement $announcement) use ($items) {
                $items->push([
                    'id' => 'announcement-'.$announcement->id,
                    'title' => 'New announcement: '.$announcement->title,
                    'body' => str($announcement->body)->limit(120),
                    'at' => $announcement->publish_at,
                ]);
            });

        Certificate::query()
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->take(5)
            ->get()
            ->each(function (Certificate $certificate) use ($items) {
                $items->push([
                    'id' => 'certificate-'.$certificate->id,
                    'title' => 'Certificate issued',
                    'body' => $certificate->course?->title ?? 'Course completed',
                    'at' => $certificate->issued_at,
                ]);
            });

        AssignmentFeedback::query()
            ->whereHas('submission', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(5)
            ->get()
            ->each(function (AssignmentFeedback $feedback) use ($items) {
                $items->push([
                    'id' => 'feedback-'.$feedback->id,
                    'title' => 'Assignment graded',
                    'body' => 'Marks: '.$feedback->marks_awarded,
                    'at' => $feedback->created_at,
                ]);
            });

        /** @var Collection<int, array{id: string, title: string, body: string, at: mixed}> $sorted */
        $sorted = $items->sortByDesc('at')->values();

        return view('learner.notifications.index', [
            'notifications' => $sorted,
            'readIds' => $readIds,
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $read = collect($request->session()->get('read_notifications', []));
        $read->push($notification);
        $request->session()->put('read_notifications', $read->unique()->values()->all());

        return back()->with('success', 'Marked as read.');
    }
}
