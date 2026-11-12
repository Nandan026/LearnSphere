@extends('layouts.app')
@section('title', 'Learner dashboard')
@section('breadcrumb', 'Learning overview')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>My learning</h1>
        <p class="ls-lead">Resume courses, check progress, and jump back into lessons.</p>
    </div>
    <a href="{{ route('catalogue.index') }}" class="btn btn-ls-primary"><i class="bi bi-plus-lg me-1"></i> Find a course</a>
</div>

<x-guide title="Learner guide" :items="['Open a course card to resume the player.', 'Progress bars update when you mark lessons complete.', 'Certificates appear after a course is finished.']" />

<div class="row g-3 mb-4">
    <div class="col-md-4"><x-stat-card label="Active enrollments" :value="$enrollments->count()" icon="bi-book" href="{{ route('catalogue.index') }}" hint="Tap to browse more" /></div>
    <div class="col-md-4"><x-stat-card label="Completed courses" :value="$certificateCount" icon="bi-award" href="{{ route('learner.certificates.index') }}" hint="View certificates" /></div>
    <div class="col-md-4"><x-stat-card label="Learning paths" value="Explore" icon="bi-signpost-split" href="{{ route('learner.paths.index') }}" hint="Guided sequences" /></div>
</div>

<div class="card ls-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Continue learning</span>
        <input type="search" class="form-control form-control-sm" style="max-width:220px" placeholder="Filter my courses" data-ls-filter-input>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($enrollments as $enrollment)
            <a href="{{ route('learner.player.show', $enrollment->course) }}"
               class="list-group-item list-group-item-action py-3"
               data-ls-filter-item="{{ $enrollment->course->title }}">
                <div class="d-flex justify-content-between gap-3 align-items-start">
                    <div>
                        <div class="fw-semibold">{{ $enrollment->course->title }}</div>
                        <div class="small text-muted">Enrolled {{ $enrollment->enrolled_at?->diffForHumans() }}</div>
                    </div>
                    <div class="text-end" style="min-width:140px">
                        <div class="small fw-semibold mb-1">{{ $enrollment->progress_percent }}%</div>
                        <div class="ls-progress" data-value="{{ $enrollment->progress_percent }}"><span></span></div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-4 text-center">
                <p class="text-muted mb-3">You are not enrolled in any course yet.</p>
                <a href="{{ route('catalogue.index') }}" class="btn btn-ls-primary">Browse catalogue</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
