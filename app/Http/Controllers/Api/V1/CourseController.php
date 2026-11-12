<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->where('status', 'published')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->latest('published_at')
            ->paginate(15);

        return response()->json($courses);
    }

    public function show(Course $course): JsonResponse
    {
        abort_unless($course->status === 'published', 404);
        $course->load(['category', 'instructor:id,name', 'sections.lessons']);

        return response()->json($course);
    }
}
