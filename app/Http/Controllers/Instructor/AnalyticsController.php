<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Services\EnrollmentService;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function show(Course $course): View
    {
        $this->authorizeInstructorCourse($course);

        $enrollments = Enrollment::query()->where('course_id', $course->id)->count();
        $completions = Enrollment::query()->where('course_id', $course->id)->whereNotNull('completed_at')->count();
        $activeLearners = LessonProgress::query()
            ->whereHas('enrollment', fn ($q) => $q->where('course_id', $course->id))
            ->distinct('enrollment_id')
            ->count('enrollment_id');

        return view('instructor.analytics.show', compact('course', 'enrollments', 'completions', 'activeLearners'));
    }
}
