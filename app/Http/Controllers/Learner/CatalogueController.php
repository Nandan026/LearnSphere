<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->with(['category', 'instructor'])
            ->where('status', 'published')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = CourseCategory::query()->where('status', 'active')->orderBy('sort_order')->get();

        return view('learner.catalogue.index', compact('courses', 'categories'));
    }

    public function show(Course $course): View
    {
        abort_unless($course->status === 'published', 404);
        $course->load([
            'category',
            'instructor',
            'sections' => fn ($q) => $q->orderBy('sort_order'),
            'sections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'tags',
            'reviews' => fn ($q) => $q->where('status', 'published')->latest()->limit(5),
        ]);

        $enrolled = Auth::check() && Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        return view('learner.catalogue.show', compact('course', 'enrolled'));
    }
}
