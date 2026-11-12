@extends('layouts.app')
@section('title', $course->title)
@section('breadcrumb', 'Catalogue')

@section('content')
<div class="ls-page-header">
    <div>
        <div class="d-flex flex-wrap gap-2 mb-2">
            <span class="badge bg-mint text-teal">{{ $course->category?->name }}</span>
            <span class="badge text-bg-light border">{{ $course->level }}</span>
            <span class="badge text-bg-light border">{{ strtoupper($course->language) }}</span>
        </div>
        <h1>{{ $course->title }}</h1>
        <p class="ls-lead">{{ $course->short_description }}</p>
    </div>
    <div class="text-end">
        <div class="fs-3 fw-bold text-teal mb-2">{{ $course->price > 0 ? '₹'.number_format((float) $course->price, 2) : 'Free' }}</div>
        @if ($enrolled ?? false)
            <a href="{{ route('learner.player.show', $course) }}" class="btn btn-ls-primary">Continue learning</a>
        @else
            <form method="POST" action="{{ route('catalogue.enroll', $course) }}">
                @csrf
                <button class="btn btn-ls-primary">{{ $course->price > 0 ? 'Buy with simulated checkout' : 'Enroll free' }}</button>
            </form>
        @endif
    </div>
</div>

<x-guide title="Course detail guide" :items="['Preview lessons may be visible before enroll.', 'Free enroll starts learning immediately.', 'Paid courses redirect to simulated checkout.']" />

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card ls-card mb-3">
            <div class="card-header">About this course</div>
            <div class="card-body">{!! nl2br(e($course->description ?? 'No description yet.')) !!}</div>
        </div>
        <div class="card ls-card">
            <div class="card-header">Curriculum</div>
            <div class="list-group list-group-flush">
                @forelse ($course->sections as $section)
                    <div class="list-group-item">
                        <div class="fw-semibold mb-2">{{ $section->title }}</div>
                        <ul class="mb-0 small text-muted">
                            @foreach ($section->lessons as $lesson)
                                <li class="mb-1">
                                    <i class="bi bi-play-circle me-1 text-teal"></i>{{ $lesson->title }}
                                    @if ($lesson->is_preview)<span class="badge bg-mint text-teal ms-1">Preview</span>@endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="list-group-item text-muted">Curriculum coming soon.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card ls-card mb-3">
            <div class="card-body">
                <div class="small text-muted">Instructor</div>
                <div class="fw-semibold">{{ $course->instructor?->name }}</div>
                <div class="small text-muted mt-3">Estimated time</div>
                <div>{{ $course->estimated_minutes ? $course->estimated_minutes.' minutes' : 'Self-paced' }}</div>
            </div>
        </div>
        @if (($course->reviews ?? collect())->count())
            <div class="card ls-card">
                <div class="card-header">Reviews</div>
                <ul class="list-group list-group-flush">
                    @foreach ($course->reviews->take(5) as $review)
                        <li class="list-group-item small">
                            <strong>{{ $review->rating }}/5</strong> — {{ Str::limit($review->review, 80) }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
