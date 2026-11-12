<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $enrollmentsByCourse = Enrollment::query()
            ->selectRaw('course_id, count(*) as total')
            ->groupBy('course_id')
            ->get();

        $coursesById = Course::query()
            ->whereIn('id', $enrollmentsByCourse->pluck('course_id'))
            ->pluck('title', 'id');

        $completions = Enrollment::query()->whereNotNull('completed_at')->count();
        $revenue = (float) Order::query()->where('status', 'paid')->sum('total_amount');
        $publishedCourses = Course::query()->where('status', 'published')->count();

        return view('admin.reports.index', compact('enrollmentsByCourse', 'coursesById', 'completions', 'revenue', 'publishedCourses'));
    }
}
