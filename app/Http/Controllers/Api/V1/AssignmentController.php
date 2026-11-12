<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function submit(Request $request, Assignment $assignment): JsonResponse
    {
        Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $assignment->course_id)
            ->firstOrFail();

        $data = $request->validate([
            'submission_text' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'],
        ]);

        $submission = AssignmentSubmission::query()->updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => Auth::id()],
            [
                'submission_text' => $data['submission_text'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ],
        );

        if ($request->hasFile('file')) {
            $submission->file_path = $request->file('file')->store('submissions', 'public');
            $submission->save();
        }

        return response()->json($submission, 201);
    }
}
