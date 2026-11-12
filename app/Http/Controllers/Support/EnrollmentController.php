<?php

declare(strict_types=1);

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::query()
            ->with(['user', 'course'])
            ->latest('enrolled_at')
            ->paginate(25);

        return view('support.enrollments.index', compact('enrollments'));
    }
}
