@extends('layouts.app')
@section('title', 'Edit course')
@section('content')
<x-guide title="Edit course guide" :items="['Update marketing copy and metadata.','Price changes affect new checkouts only.','Keep level and language accurate.']" />
<div class="card ls-card"><div class="card-body">
<form method="POST" action="{{ route('instructor.courses.update', $course) }}">@csrf @method('PUT')
@include('instructor.courses._form', ['course' => $course, 'categories' => $categories])
<button class="btn btn-ls-primary mt-3">Save</button>
</form></div></div>
@endsection
