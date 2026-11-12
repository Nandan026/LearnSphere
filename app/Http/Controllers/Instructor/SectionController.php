<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $maxOrder = (int) $course->sections()->max('sort_order');

        $course->sections()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'sort_order' => $maxOrder + 1,
            'is_published' => true,
        ]);

        return back()->with('success', 'Section added.');
    }

    public function update(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($section->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $section->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_published' => $request->boolean('is_published', $section->is_published),
        ]);

        return back()->with('success', 'Section updated.');
    }

    public function destroy(Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($section->course_id === $course->id, 404);
        $section->delete();

        return back()->with('success', 'Section removed.');
    }

    public function reorder(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);

        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:course_sections,id'],
        ]);

        foreach ($data['order'] as $index => $sectionId) {
            CourseSection::query()
                ->where('course_id', $course->id)
                ->where('id', $sectionId)
                ->update(['sort_order' => $index + 1]);
        }

        return back()->with('success', 'Sections reordered.');
    }
}
