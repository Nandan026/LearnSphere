<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function store(Course $course): RedirectResponse
    {
        abort_unless($course->status === 'published', 404);

        $user = Auth::user();

        $exists = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->route('learner.player.show', $course);
        }

        if ((float) $course->price > 0) {
            return redirect()->route('learner.orders.checkout', $course);
        }

        $this->enrollmentService->enroll($user, $course, 'free');

        return redirect()->route('learner.player.show', $course)->with('success', 'You are enrolled.');
    }
}
