<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\CertificateService;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CoursePlayerController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
        private readonly CertificateService $certificateService,
    ) {}

    public function show(Course $course, ?Lesson $lesson = null): View
    {
        $enrollment = $this->requireEnrollment($course);
        $course->load([
            'sections' => fn ($q) => $q->orderBy('sort_order'),
            'sections.lessons' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order'),
            'quizzes' => fn ($q) => $q->where('status', 'published'),
            'assignments' => fn ($q) => $q->where('status', 'published'),
        ]);

        if ($lesson === null) {
            $lesson = $course->sections->flatMap->lessons->first();
        }

        abort_unless($lesson === null || $lesson->course_id === $course->id, 404);

        $progressPercent = $this->enrollmentService->progressPercent($enrollment);
        $bookmarked = $lesson ? Bookmark::query()
            ->where('user_id', Auth::id())
            ->where('lesson_id', $lesson->id)
            ->exists() : false;

        $completedLessonIds = $enrollment->lessonProgress()
            ->where('status', 'completed')
            ->pluck('lesson_id');

        return view('learner.player.show', compact(
            'course',
            'enrollment',
            'lesson',
            'progressPercent',
            'bookmarked',
            'completedLessonIds',
        ));
    }

    public function complete(Course $course, Lesson $lesson): RedirectResponse
    {
        $enrollment = $this->requireEnrollment($course);
        abort_unless($lesson->course_id === $course->id, 404);

        $this->enrollmentService->markLessonComplete($enrollment, $lesson);
        $enrollment->refresh();
        $this->certificateService->issueIfEligible($enrollment);

        return back()->with('success', 'Lesson marked complete.');
    }

    public function bookmark(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $enrollment = $this->requireEnrollment($course);
        abort_unless($lesson->course_id === $course->id, 404);

        $bookmark = Bookmark::query()->firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        if ($request->boolean('remove')) {
            $bookmark->delete();

            return back()->with('success', 'Bookmark removed.');
        }

        return back()->with('success', 'Lesson bookmarked.');
    }

    private function requireEnrollment(Course $course): Enrollment
    {
        return Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();
    }
}
