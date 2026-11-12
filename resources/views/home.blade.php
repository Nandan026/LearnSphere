@extends('layouts.guest')

@section('title', 'LearnSphere — Learn without limits')

@section('content')
<section class="ls-hero">
    <div class="ls-hero-inner">
        <p class="brand-signal mb-3">LearnSphere</p>
        <h1 class="mb-3">Learn, practice, and prove your skills in one workspace.</h1>
        <p class="lead mb-4 opacity-90 mx-auto" style="max-width: 34rem;">
            Browse courses, track progress, take quizzes, and earn verifiable certificates.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ auth()->check() ? route('catalogue.index') : route('login') }}" class="btn btn-light btn-lg px-4">
                Browse catalogue <i class="bi bi-arrow-right ms-1"></i>
            </a>
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Sign in with demo account</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">Open dashboard</a>
            @endguest
        </div>
    </div>
</section>

<div class="d-flex justify-content-between align-items-end mb-3">
    <div>
        <h2 class="h4 mb-1">Featured courses</h2>
        <p class="text-muted mb-0">Start with a published course from the catalogue.</p>
    </div>
    <a href="{{ auth()->check() ? route('catalogue.index') : route('login') }}" class="btn btn-sm btn-outline-secondary">View all</a>
</div>

<div class="row g-3 mb-4">
    @forelse ($featured as $course)
        <div class="col-md-4">
            <a href="{{ auth()->check() ? route('catalogue.show', $course) : route('login') }}" class="card ls-card course-tile ls-card-hover h-100 text-decoration-none text-dark">
                <div class="course-cover">
                    <span class="badge">{{ $course->category?->name ?? 'Course' }}</span>
                </div>
                <div class="card-body">
                    <h3 class="h5">{{ $course->title }}</h3>
                    <p class="small text-muted mb-3">{{ Str::limit($course->short_description, 90) }}</p>
                    <div class="d-flex justify-content-between align-items-center small">
                        <strong class="text-teal">{{ $course->price > 0 ? '₹'.number_format((float) $course->price, 2) : 'Free' }}</strong>
                        <span class="text-muted">{{ $course->level }}</span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-light border">No published courses yet.</div></div>
    @endforelse
</div>
@endsection
