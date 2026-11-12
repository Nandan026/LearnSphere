<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Concerns\EnsuresInstructorOwnsCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LessonController extends Controller
{
    use EnsuresInstructorOwnsCourse;

    public function store(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($section->course_id === $course->id, 404);

        $data = $this->validateLesson($request);
        $maxOrder = (int) $section->lessons()->max('sort_order');

        $section->lessons()->create([
            ...$data,
            'course_id' => $course->id,
            'slug' => $this->uniqueLessonSlug($course, $data['title']),
            'sort_order' => $maxOrder + 1,
            'is_published' => $request->boolean('is_published', true),
            'is_preview' => $request->boolean('is_preview', false),
        ]);

        return back()->with('success', 'Lesson created.');
    }

    public function update(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($lesson->course_id === $course->id, 404);

        $lesson->update($this->validateLesson($request) + [
            'is_published' => $request->boolean('is_published', $lesson->is_published),
            'is_preview' => $request->boolean('is_preview', $lesson->is_preview),
        ]);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeInstructorCourse($course);
        abort_unless($lesson->course_id === $course->id, 404);
        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLesson(Request $request): array
    {
        return $request->validate([
            'section_id' => ['required', 'exists:course_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', Rule::in(['text', 'video', 'file', 'link', 'embed'])],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueLessonSlug(Course $course, string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Lesson::query()->where('course_id', $course->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
