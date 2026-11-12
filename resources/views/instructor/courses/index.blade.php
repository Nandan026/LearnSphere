@extends('layouts.app')
@section('title', 'My courses')
@section('breadcrumb', 'Instructor')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>My courses</h1>
        <p class="ls-lead">Create drafts, build curriculum, then submit for review or publish.</p>
    </div>
    <a href="{{ route('instructor.courses.create') }}" class="btn btn-ls-primary"><i class="bi bi-plus-lg me-1"></i> New course</a>
</div>

<x-guide title="Course list guide" :items="['Open a course hub to manage sections and lessons.', 'Submit drafts for review when ready.', 'Use analytics after learners enroll.']" />

<div class="card ls-card mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <input type="search" class="form-control" style="max-width:280px" placeholder="Filter by title or status" data-ls-filter-input>
        <span class="small text-muted">{{ $courses->total() }} courses</span>
    </div>
</div>

<div class="row g-3">
@forelse ($courses as $course)
    <div class="col-md-6 col-xl-4" data-ls-filter-item="{{ $course->title }} {{ $course->status }}">
        <div class="card ls-card ls-card-hover h-100">
            <div class="course-cover">
                <span class="badge">{{ $course->category?->name ?? 'Uncategorized' }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <h2 class="h5 mb-0">{{ $course->title }}</h2>
                    <span class="badge bg-mint text-teal text-uppercase">{{ $course->status }}</span>
                </div>
                <p class="small text-muted">{{ Str::limit($course->short_description, 90) }}</p>
                <div class="small text-muted mb-3">
                    {{ $course->price > 0 ? '₹'.number_format((float) $course->price, 2) : 'Free' }}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-sm btn-ls-primary flex-fill">Open hub</a>
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="card ls-card p-5 text-center">
            <p class="text-muted mb-3">No courses yet.</p>
            <a href="{{ route('instructor.courses.create') }}" class="btn btn-ls-primary">Create your first course</a>
        </div>
    </div>
@endforelse
</div>
<div class="mt-3">{{ $courses->links() }}</div>
@endsection
