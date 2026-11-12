<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AssignmentSubmissionController extends Controller
{
    public function create(Assignment $assignment): View
    {
        $this->assertEnrolled($assignment);

        $submission = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('learner.assignments.submit', compact('assignment', 'submission'));
    }

    public function store(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->assertEnrolled($assignment);

        $data = $request->validate([
            'submission_text' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'],
        ]);

        if (empty($data['submission_text']) && ! $request->hasFile('file')) {
            return back()->withErrors(['submission_text' => 'Provide text or a file.']);
        }

        $submission = AssignmentSubmission::query()->updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
            ],
            [
                'submission_text' => $data['submission_text'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ],
        );

        if ($request->hasFile('file')) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $submission->file_path = $request->file('file')->store('submissions', 'public');
            $submission->save();
        }

        return back()->with('success', 'Assignment submitted.');
    }

    private function assertEnrolled(Assignment $assignment): void
    {
        Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->firstOrFail();
    }
}
