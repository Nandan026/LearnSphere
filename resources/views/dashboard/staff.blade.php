@extends('layouts.app')
@section('title', 'Staff dashboard')
@section('breadcrumb', 'Operations')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>{{ $isContent ? 'Content operations' : 'Support desk' }}</h1>
        <p class="ls-lead">
            {{ $isContent ? 'Review and publish courses waiting in the queue.' : 'Look up learners and enrollments (read-only).' }}
        </p>
    </div>
    @if ($isContent)
        <a href="{{ route('content.moderation.index') }}" class="btn btn-ls-primary">Open moderation queue</a>
    @else
        <a href="{{ route('support.users.index') }}" class="btn btn-ls-primary">Browse users</a>
    @endif
</div>

<x-guide :items="[
    $isContent ? 'Use Publish courses to release reviewed content.' : 'Use Support desk to look up users and enrollments (read-only).',
    'Coordinate with instructors on course status.',
    'Escalate account issues to a super admin when needed.',
]" title="Staff operations guide" />

<div class="row g-3">
    <div class="col-md-6">
        <x-stat-card
            label="Courses awaiting action"
            :value="$pendingCourses"
            icon="bi-hourglass"
            :href="$isContent ? route('content.moderation.index') : null"
        />
    </div>
    <div class="col-md-6">
        <x-stat-card
            label="Enrollments"
            :value="$enrollmentCount"
            icon="bi-people"
            :href="$isContent ? null : route('support.enrollments.index')"
        />
    </div>
</div>
@endsection
