<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DiscussionThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscussionModerationController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $threads = DiscussionThread::query()
            ->with(['course', 'user'])
            ->when(! $user->hasRole('super-admin'), function ($query) use ($user) {
                $query->whereHas('course', fn ($q) => $q->where('instructor_id', $user->id));
            })
            ->latest()
            ->paginate(20);

        return view('instructor.discussions.index', compact('threads'));
    }
}
