<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use Illuminate\View\View;

class LearningPathController extends Controller
{
    public function index(): View
    {
        $paths = LearningPath::query()
            ->where('status', 'published')
            ->withCount('courses')
            ->latest()
            ->paginate(12);

        return view('learner.paths.index', compact('paths'));
    }

    public function show(LearningPath $path): View
    {
        abort_unless($path->status === 'published', 404);
        $path->load(['courses' => fn ($q) => $q->orderByPivot('sort_order')]);

        return view('learner.paths.show', compact('path'));
    }
}
