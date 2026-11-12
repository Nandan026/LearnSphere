<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Carbon;

class EnrollmentService
{
    public function enroll(User $user, Course $course, string $type = 'free'): Enrollment
    {
        $existing = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return Enrollment::query()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_type' => $type,
            'status' => 'active',
            'enrolled_at' => Carbon::now(),
        ]);
    }

    public function progressPercent(Enrollment $enrollment): float
    {
        $enrollment->loadMissing('course');

        $publishedTotal = Lesson::query()
            ->where('course_id', $enrollment->course_id)
            ->where('is_published', true)
            ->count();

        if ($publishedTotal === 0) {
            return 0.0;
        }

        $completedCount = LessonProgress::query()
            ->where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->whereHas('lesson', fn ($query) => $query->where('is_published', true))
            ->count();

        return round(($completedCount / $publishedTotal) * 100, 2);
    }

    public function markLessonComplete(Enrollment $enrollment, Lesson $lesson): void
    {
        if ($lesson->course_id !== $enrollment->course_id) {
            return;
        }

        $now = Carbon::now();

        LessonProgress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => 'completed',
                'started_at' => $now,
                'completed_at' => $now,
            ],
        );

        if ($enrollment->completed_at !== null) {
            return;
        }

        $publishedLessonIds = Lesson::query()
            ->where('course_id', $enrollment->course_id)
            ->where('is_published', true)
            ->pluck('id');

        if ($publishedLessonIds->isEmpty()) {
            return;
        }

        $completedLessonIds = LessonProgress::query()
            ->where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->whereIn('lesson_id', $publishedLessonIds)
            ->pluck('lesson_id');

        if ($completedLessonIds->count() === $publishedLessonIds->count()) {
            $enrollment->update(['completed_at' => $now]);
        }
    }
}
