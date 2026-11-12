<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function index(): View
    {
        $courses = $this->instructorCoursesQuery()
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('instructor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('instructor.courses.create', [
            'categories' => CourseCategory::query()->where('status', 'active')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedCourse($request);

        $course = Course::query()->create([
            ...$data,
            'instructor_id' => Auth::id(),
            'slug' => $this->uniqueSlug($data['title']),
            'status' => 'draft',
        ]);

        return redirect()->route('instructor.courses.show', $course)->with('success', 'Course created.');
    }

    public function show(Course $course): View
    {
        $this->authorizeInstructorCourse($course);
        $course->load(['sections.lessons', 'quizzes', 'assignments', 'announcements']);

        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $this->authorizeInstructorCourse($course);

        return view('instructor.courses.edit', [
            'course' => $course,
            'categories' => CourseCategory::query()->where('status', 'active')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        $data = $this->validatedCourse($request);
        $course->update($data);

        return back()->with('success', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        $course->delete();

        return redirect()->route('instructor.courses.index')->with('success', 'Course deleted.');
    }

    public function submitForReview(Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        $course->update(['status' => 'in_review']);

        return back()->with('success', 'Course submitted for review.');
    }

    public function publish(Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        if (! Auth::user()?->hasRole('super-admin')) {
            abort(403, 'Only admins can publish directly.');
        }

        $course->update(['status' => 'published', 'published_at' => now()]);

        return back()->with('success', 'Course published.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedCourse(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:course_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'language' => ['required', 'string', 'max:10'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Course::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
