<?php

declare(strict_types=1);

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CourseModerationController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function index(Request $request): View
    {
        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->whereIn('status', ['draft', 'in_review', 'published'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('content.moderation.index', compact('courses'));
    }

    public function publish(Request $request, Course $course): RedirectResponse
    {
        $course->update([
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);

        $this->auditLogger->log($request->user(), 'course.published', Course::class, $course->id);

        return back()->with('success', 'Course published.');
    }
}
