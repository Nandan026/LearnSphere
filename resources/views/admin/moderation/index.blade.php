@extends('layouts.app')
@section('title', 'Course moderation')
@section('content')
<x-guide title="Moderation guide" :items="['Filter by draft, in_review, or published.','Publish sets published_at timestamp.','Archive removes courses from the catalogue.']" />
<form class="mb-3" method="GET"><select name="status" class="form-select form-select-sm w-auto d-inline-block" onchange="this.form.submit()">
<option value="">All statuses</option>
@foreach (['draft','in_review','published','archived'] as $st)<option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>@endforeach
</select></form>
<div class="card ls-card table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Course</th><th>Instructor</th><th>Status</th><th></th></tr></thead><tbody>
@foreach ($courses as $course)
<tr>
<td>{{ $course->title }}</td>
<td>{{ $course->instructor?->name }}</td>
<td>{{ $course->status }}</td>
<td class="text-nowrap">
@if ($course->status !== 'published')
<form class="d-inline" method="POST" action="{{ route('admin.moderation.publish', $course) }}">@csrf<button class="btn btn-sm btn-ls-primary">Publish</button></form>
@endif
@if ($course->status !== 'archived')
<form class="d-inline" method="POST" action="{{ route('admin.moderation.archive', $course) }}">@csrf<button class="btn btn-sm btn-outline-secondary">Archive</button></form>
@endif
</td>
</tr>
@endforeach
</tbody></table></div>
{{ $courses->links() }}
@endsection
