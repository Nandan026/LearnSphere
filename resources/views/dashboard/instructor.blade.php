@extends('layouts.app')
@section('title', 'Instructor dashboard')
@section('breadcrumb', 'Teaching overview')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>Instructor workspace</h1>
        <p class="ls-lead">Build courses, publish content, and track enrollments.</p>
    </div>
    <a href="{{ route('instructor.courses.create') }}" class="btn btn-ls-primary"><i class="bi bi-plus-lg me-1"></i> New course</a>
</div>

<x-guide title="Instructor guide" :items="['Create a draft course, then add sections and lessons.', 'Use quizzes and assignments from the course hub.', 'Submit for review when content is ready.']" />

<div class="row g-3 mb-4">
    <div class="col-md-4"><x-stat-card label="Published" :value="$published" icon="bi-check-circle" href="{{ route('instructor.courses.index') }}" /></div>
    <div class="col-md-4"><x-stat-card label="Drafts" :value="$drafts" icon="bi-pencil" href="{{ route('instructor.courses.index') }}" /></div>
    <div class="col-md-4"><x-stat-card label="Total courses" :value="$courses->count()" icon="bi-collection" href="{{ route('instructor.courses.index') }}" /></div>
</div>

<div class="card ls-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Your courses</span>
        <input type="search" class="form-control form-control-sm" style="max-width:220px" placeholder="Filter courses" data-ls-filter-input>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Course</th><th>Status</th><th>Enrollments</th><th></th></tr></thead>
            <tbody>
            @forelse ($courses as $course)
                <tr data-ls-filter-item="{{ $course->title }} {{ $course->status }}">
                    <td class="fw-semibold">{{ $course->title }}</td>
                    <td><span class="badge bg-mint text-teal text-uppercase">{{ $course->status }}</span></td>
                    <td>{{ $course->enrollments_count }}</td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('instructor.courses.show', $course) }}">Open hub</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted p-4">No courses yet. Create your first course.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
