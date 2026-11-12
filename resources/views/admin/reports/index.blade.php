@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<x-guide title="Reports guide" :items="['Enrollment totals are grouped per course.','Completion counts use completed_at on enrollments.','Revenue sums paid simulated orders only.']" />
<div class="row g-3 mb-4">
    <div class="col-md-4"><x-stat-card label="Completions" :value="$completions" icon="bi-check2-circle" /></div>
    <div class="col-md-4"><x-stat-card label="Published courses" :value="$publishedCourses" icon="bi-journal-check" /></div>
    <div class="col-md-4"><x-stat-card label="Revenue (sim)" :value="'₹'.number_format($revenue, 2)" icon="bi-currency-rupee" /></div>
</div>
<div class="card ls-card"><div class="card-header">Enrollments by course</div>
<ul class="list-group list-group-flush">@forelse($enrollmentsByCourse as $row)
<li class="list-group-item d-flex justify-content-between"><span>{{ $coursesById[$row->course_id] ?? 'Course #'.$row->course_id }}</span><span>{{ $row->total }}</span></li>
@empty <li class="list-group-item text-muted">No enrollments yet.</li> @endforelse
</ul></div>
@endsection
