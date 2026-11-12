@extends('layouts.app')
@section('title', $course->title.' — Player')
@section('breadcrumb', 'Course player')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>{{ $course->title }}</h1>
        <p class="ls-lead mb-2">{{ $lesson?->title ?? 'Select a lesson to begin' }}</p>
        <div class="d-flex align-items-center gap-3" style="max-width:320px">
            <div class="ls-progress flex-fill" data-value="{{ $progressPercent }}"><span></span></div>
            <strong class="small">{{ $progressPercent }}%</strong>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('learner.discussions.index', $course) }}" class="btn btn-outline-secondary"><i class="bi bi-chat-dots me-1"></i>Discussions</a>
        <a href="{{ route('catalogue.show', $course) }}" class="btn btn-outline-secondary">Course info</a>
    </div>
</div>

<x-guide title="Course player guide" :items="['Pick lessons from the curriculum sidebar.', 'Mark complete to advance your progress bar.', 'Bookmark important lessons for quick return.']" />

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card ls-card curriculum">
            <div class="card-header d-flex justify-content-between">
                <span>Curriculum</span>
                <span class="badge bg-mint text-teal">{{ $progressPercent }}%</span>
            </div>
            <div class="list-group list-group-flush small" style="max-height:70vh; overflow:auto;">
                @foreach ($course->sections as $section)
                    <div class="list-group-item fw-semibold bg-mint border-0">{{ $section->title }}</div>
                    @foreach ($section->lessons as $item)
                        <a href="{{ route('learner.player.lesson', [$course, $item]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $lesson && $lesson->id === $item->id ? 'active' : '' }}">
                            <span><i class="bi bi-play-circle me-1"></i>{{ $item->title }}</span>
                            @if (($completedLessonIds ?? collect())->contains($item->id))
                                <i class="bi bi-check-circle-fill lesson-done"></i>
                            @endif
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        @if ($lesson)
            <div class="card ls-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <span class="badge text-bg-light border text-uppercase">{{ $lesson->lesson_type }}</span>
                            <h2 class="h4 mt-2 mb-0">{{ $lesson->title }}</h2>
                        </div>
                    </div>

                    @if ($lesson->lesson_type === 'video' && $lesson->video_url)
                        <div class="ratio ratio-16x9 mb-3 rounded overflow-hidden border">
                            <iframe src="{{ $lesson->video_url }}" title="{{ $lesson->title }}" allowfullscreen loading="lazy"></iframe>
                        </div>
                    @endif

                    <div class="lesson-content lh-lg">{!! $lesson->content !!}</div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <form method="POST" action="{{ route('learner.player.complete', [$course, $lesson]) }}">@csrf
                            <button class="btn btn-ls-primary"><i class="bi bi-check2-circle me-1"></i>Mark complete</button>
                        </form>
                        <form method="POST" action="{{ route('learner.player.bookmark', [$course, $lesson]) }}">@csrf
                            <button class="btn btn-outline-secondary">
                                <i class="bi {{ $bookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                                {{ $bookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        </form>
                        @if ($bookmarked)
                            <form method="POST" action="{{ route('learner.player.bookmark', [$course, $lesson]) }}">@csrf
                                <input type="hidden" name="remove" value="1">
                                <button class="btn btn-outline-danger">Remove bookmark</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @php $publishedQuizzes = $course->quizzes->where('status', 'published'); @endphp
            @if ($publishedQuizzes->isNotEmpty())
                <div class="card ls-card mb-3">
                    <div class="card-header">Assessments</div>
                    <div class="list-group list-group-flush">
                        @foreach ($publishedQuizzes as $quiz)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $quiz->title }}</div>
                                    <div class="small text-muted">Pass {{ $quiz->pass_percentage }}% · {{ $quiz->max_attempts ? $quiz->max_attempts.' attempts' : 'Unlimited' }}</div>
                                </div>
                                <form method="POST" action="{{ route('learner.quizzes.start', $quiz) }}">@csrf
                                    <button class="btn btn-sm btn-ls-primary">Start quiz</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (($course->assignments ?? collect())->where('status', 'published')->isNotEmpty())
                <div class="card ls-card">
                    <div class="card-header">Assignments</div>
                    <div class="list-group list-group-flush">
                        @foreach ($course->assignments->where('status', 'published') as $assignment)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('learner.assignments.submit', $assignment) }}">
                                <span>{{ $assignment->title }}</span>
                                <span class="small text-muted">Submit</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="card ls-card p-5 text-center">
                <i class="bi bi-journal-text display-6 text-teal"></i>
                <p class="mt-3 mb-0 text-muted">No published lessons yet. Check back soon.</p>
            </div>
        @endif
    </div>
</div>
@endsection
