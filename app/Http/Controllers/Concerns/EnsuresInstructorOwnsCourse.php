<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

trait EnsuresInstructorOwnsCourse
{
    protected function authorizeInstructorCourse(Course $course): void
    {
        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        if ($user->hasRole('super-admin')) {
            return;
        }

        if ((int) $course->instructor_id !== (int) $user->id) {
            abort(403, 'You do not own this course.');
        }
    }

    protected function instructorCoursesQuery()
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return Course::query();
        }

        return Course::query()->where('instructor_id', $user->id);
    }
}
