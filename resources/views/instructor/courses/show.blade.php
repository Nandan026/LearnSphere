@extends('layouts.app')
@section('title', $course->title)
@section('breadcrumb', 'Course hub')

@section('content')
<div class="ls-page-header">
    <div>
        <div class="d-flex flex-wrap gap-2 mb-2">
            <span class="badge bg-mint text-teal text-uppercase">{{ $course->status }}</span>
            <span class="badge text-bg-light border">{{ $course->category?->name }}</span>
        </div>
        <h1>{{ $course->title }}</h1>
        <p class="ls-lead">Build sections, lessons, quizzes, and more from this hub.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @if ($course->status === 'draft')
            <form method="POST" action="{{ route('instructor.courses.submit-review', $course) }}">@csrf
                <button class="btn btn-ls-primary">Submit for review</button>
            </form>
        @endif
        <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-outline-secondary">Edit details</a>
    </div>
</div>

<x-guide title="Course builder guide" :items="[
    'Add a section first, then add lessons inside it.',
    'Use the module chips to open quizzes, assignments, and live sessions.',
    'Submit for review when the course is ready for publishing.',
]" />

<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach ([
        ['Analytics', 'instructor.analytics.show', 'bi-graph-up'],
        ['Question banks', 'instructor.question-banks.index', 'bi-collection'],
        ['Quizzes', 'instructor.quizzes.index', 'bi-ui-checks'],
        ['Assignments', 'instructor.assignments.index', 'bi-file-earmark-text'],
        ['Announcements', 'instructor.announcements.index', 'bi-megaphone'],
        ['Live sessions', 'instructor.live-sessions.index', 'bi-camera-video'],
    ] as [$label, $route, $icon])
        <a href="{{ route($route, $course) }}" class="btn btn-outline-secondary">
            <i class="bi {{ $icon }} me-1"></i>{{ $label }}
        </a>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><x-stat-card label="Sections" :value="$course->sections->count()" icon="bi-list-nested" /></div>
    <div class="col-md-4"><x-stat-card label="Lessons" :value="$course->sections->sum(fn($s) => $s->lessons->count())" icon="bi-play-btn" /></div>
    <div class="col-md-4"><x-stat-card label="Quizzes" :value="$course->quizzes->count()" icon="bi-ui-checks" /></div>
</div>

@foreach ($course->sections as $section)
    <div class="card ls-card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ $section->title }}</strong>
            <span class="badge text-bg-light border">{{ $section->lessons->count() }} lessons</span>
        </div>
        <div class="card-body">
            @if ($section->description)
                <p class="small text-muted">{{ $section->description }}</p>
            @endif
            <div class="list-group list-group-flush mb-3">
                @forelse ($section->lessons as $lesson)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-play-circle text-teal me-2"></i>{{ $lesson->title }}
                            <span class="badge text-bg-light border ms-1 text-uppercase">{{ $lesson->lesson_type }}</span>
                            @if ($lesson->is_published)<span class="badge bg-mint text-teal ms-1">Published</span>@endif
                        </span>
                    </div>
                @empty
                    <div class="text-muted small">No lessons in this section yet.</div>
                @endforelse
            </div>

            <details class="border rounded-3 p-3 bg-sand">
                <summary class="fw-semibold text-teal" style="cursor:pointer">Add lesson</summary>
                <form method="POST" action="{{ route('instructor.lessons.store', [$course, $section]) }}" class="row g-2 mt-2">
                    @csrf
                    <div class="col-md-4"><input name="title" class="form-control" placeholder="Lesson title" required></div>
                    <div class="col-md-3">
                        <select name="lesson_type" class="form-select">
                            <option value="text">Text</option>
                            <option value="video">Video</option>
                            <option value="link">Link</option>
                        </select>
                    </div>
                    <div class="col-md-3"><input name="content" class="form-control" placeholder="Content or URL"></div>
                    <div class="col-md-2"><button class="btn btn-ls-primary w-100">Add</button></div>
                    <div class="col-12 form-check ms-1">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="pub{{ $section->id }}">
                        <label class="form-check-label" for="pub{{ $section->id }}">Publish immediately</label>
                    </div>
                </form>
            </details>
        </div>
    </div>
@endforeach

<div class="card ls-card">
    <div class="card-header">Add section</div>
    <div class="card-body">
        <form method="POST" action="{{ route('instructor.sections.store', $course) }}" class="row g-2">
            @csrf
            <div class="col-md-5"><input name="title" class="form-control" placeholder="Section title" required></div>
            <div class="col-md-5"><input name="description" class="form-control" placeholder="Short description"></div>
            <div class="col-md-2"><button class="btn btn-ls-primary w-100">Add section</button></div>
        </form>
    </div>
</div>
@endsection
