<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Course::query()
            ->where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home', compact('featured'));
    }
}
