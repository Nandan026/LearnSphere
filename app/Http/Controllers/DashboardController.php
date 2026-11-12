<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function __invoke(): View
    {
        $user = Auth::user();
        $user->loadMissing('roles');

        if ($user->hasRole('super-admin')) {
            return view('dashboard.admin', [
                'userCount' => User::query()->count(),
                'courseCount' => Course::query()->count(),
                'enrollmentCount' => Enrollment::query()->count(),
                'revenueTotal' => (float) Order::query()->where('status', 'paid')->sum('total_amount'),
                'recentAudit' => AuditLog::query()->latest()->take(5)->get(),
            ]);
        }

        if ($user->hasRole('instructor')) {
            $courses = Course::query()->where('instructor_id', $user->id)->withCount('enrollments')->get();

            return view('dashboard.instructor', [
                'courses' => $courses,
                'published' => $courses->where('status', 'published')->count(),
                'drafts' => $courses->where('status', 'draft')->count(),
            ]);
        }

        if ($user->hasAnyRole(['content-manager', 'support-staff'])) {
            return view('dashboard.staff', [
                'isContent' => $user->hasRole('content-manager'),
                'pendingCourses' => Course::query()->whereIn('status', ['draft', 'in_review'])->count(),
                'enrollmentCount' => Enrollment::query()->count(),
            ]);
        }

        $enrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->with('course')
            ->latest('enrolled_at')
            ->get()
            ->map(function (Enrollment $enrollment) {
                $enrollment->progress_percent = $this->enrollmentService->progressPercent($enrollment);

                return $enrollment;
            });

        return view('dashboard.learner', [
            'enrollments' => $enrollments,
            'certificateCount' => $user->enrollments()->whereNotNull('completed_at')->count(),
        ]);
    }
}
