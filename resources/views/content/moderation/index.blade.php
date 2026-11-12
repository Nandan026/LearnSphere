@extends('layouts.app')
@section('title', 'Publish courses')
@section('content')
<x-guide title="Content publishing guide" :items="['Content managers may publish reviewed courses.','Draft and in_review items appear here.','Publishing is logged in the audit trail.']" />
<div class="card ls-card table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Course</th><th>Instructor</th><th>Status</th><th></th></tr></thead><tbody>
@foreach ($courses as $course)
<tr>
<td>{{ $course->title }}</td><td>{{ $course->instructor?->name }}</td><td>{{ $course->status }}</td>
<td>@if(!in_array($course->status, ['published','archived'], true))
<form method="POST" action="{{ route('content.moderation.publish', $course) }}">@csrf<button class="btn btn-sm btn-ls-primary">Publish</button></form>@endif</td>
</tr>@endforeach
</tbody></table></div>
{{ $courses->links() }}
@endsection
