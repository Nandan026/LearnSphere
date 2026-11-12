<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function complete(Lesson $lesson): JsonResponse
    {
        $enrollment = Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $lesson->course_id)
            ->where('status', 'active')
            ->firstOrFail();

        $this->enrollmentService->markLessonComplete($enrollment, $lesson);

        return response()->json(['progress_percent' => $this->enrollmentService->progressPercent($enrollment)]);
    }
}
