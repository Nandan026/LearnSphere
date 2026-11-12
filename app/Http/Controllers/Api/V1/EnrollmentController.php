<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function store(Course $course): JsonResponse
    {
        abort_unless($course->status === 'published', 404);

        if ((float) $course->price > 0) {
            return response()->json(['message' => 'Paid course — use web checkout.'], 402);
        }

        $enrollment = $this->enrollmentService->enroll(Auth::user(), $course, 'free');

        return response()->json($enrollment, 201);
    }

    public function learnerCourses(): JsonResponse
    {
        $enrollments = Enrollment::query()
            ->where('user_id', Auth::id())
            ->with('course')
            ->get();

        return response()->json($enrollments);
    }
}
