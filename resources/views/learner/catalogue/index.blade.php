@extends('layouts.app')
@section('title', 'Course catalogue')
@section('breadcrumb', 'Discover')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>Course catalogue</h1>
        <p class="ls-lead">Filter published courses and open a detail page to enroll.</p>
    </div>
</div>

<x-guide title="Catalogue guide" :items="['Search by title or filter by category.', 'Free courses enroll immediately.', 'Paid courses use simulated checkout — no real cards.']" />

<form class="card ls-card mb-4" method="GET">
    <div class="card-body row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Search</label>
            <input name="q" class="form-control" placeholder="Search courses…" value="{{ request('q') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Category</label>
            <select name="category" class="form-select">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-ls-primary flex-fill">Apply filters</button>
            <a href="{{ route('catalogue.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="row g-3">
@forelse ($courses as $course)
    <div class="col-md-6 col-xl-4">
        <a href="{{ route('catalogue.show', $course) }}" class="card ls-card course-tile ls-card-hover h-100 text-decoration-none text-dark">
            <div class="course-cover">
                <div class="w-100 d-flex justify-content-between align-items-end">
                    <span class="badge">{{ $course->category?->name ?? 'General' }}</span>
                    <span class="badge">{{ $course->level }}</span>
                </div>
            </div>
            <div class="card-body">
                <h2 class="h5">{{ $course->title }}</h2>
                <p class="small text-muted">{{ Str::limit($course->short_description, 110) }}</p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <strong class="text-teal">{{ $course->price > 0 ? '₹'.number_format((float) $course->price, 2) : 'Free' }}</strong>
                    <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $course->instructor?->name }}</span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                <span class="btn btn-sm btn-outline-secondary w-100">View details</span>
            </div>
        </a>
    </div>
@empty
    <div class="col-12">
        <div class="card ls-card p-5 text-center text-muted">No courses match your filters.</div>
    </div>
@endforelse
</div>
<div class="mt-3">{{ $courses->links() }}</div>
@endsection
