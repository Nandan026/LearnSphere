@extends('layouts.app')
@section('title', 'Admin dashboard')
@section('breadcrumb', 'Platform overview')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>Admin dashboard</h1>
        <p class="ls-lead">Monitor users, courses, enrollments, and simulated revenue.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Manage users</a>
        <a href="{{ route('admin.moderation.index') }}" class="btn btn-ls-primary">Moderation</a>
    </div>
</div>

<x-guide title="Admin dashboard guide" :items="['Use the sidebar to open each admin module.', 'Click a stat card to jump into that area.', 'Audit entries help you verify recent actions.']" />

<div class="row g-3 mb-4">
    <div class="col-md-3"><x-stat-card label="Users" :value="$userCount" icon="bi-people" href="{{ route('admin.users.index') }}" /></div>
    <div class="col-md-3"><x-stat-card label="Courses" :value="$courseCount" icon="bi-journal" href="{{ route('admin.moderation.index') }}" /></div>
    <div class="col-md-3"><x-stat-card label="Enrollments" :value="$enrollmentCount" icon="bi-person-check" href="{{ route('admin.reports.index') }}" /></div>
    <div class="col-md-3"><x-stat-card label="Revenue (sim)" :value="'₹'.number_format($revenueTotal, 2)" icon="bi-currency-rupee" href="{{ route('admin.reports.index') }}" /></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card ls-card h-100">
            <div class="card-header">Recent audit activity</div>
            <ul class="list-group list-group-flush">
                @forelse ($recentAudit as $log)
                    <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="fw-semibold">{{ $log->action }}</div>
                            <div class="small text-muted">{{ $log->entity_type }} #{{ $log->entity_id }}</div>
                        </div>
                        <span class="small text-muted text-nowrap">{{ $log->created_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No audit entries yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card ls-card h-100">
            <div class="card-header">Quick actions</div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus me-2 text-teal"></i>Create user</a>
                <a class="list-group-item list-group-item-action" href="{{ route('admin.categories.create') }}"><i class="bi bi-folder-plus me-2 text-teal"></i>Add category</a>
                <a class="list-group-item list-group-item-action" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart me-2 text-teal"></i>Open reports</a>
            </div>
        </div>
    </div>
</div>
@endsection
