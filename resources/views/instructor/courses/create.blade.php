@extends('layouts.app')
@section('title', 'New course')
@section('content')
<x-guide title="New course guide" :items="['Choose category and pricing.','Free courses use price 0.','Save as draft before adding lessons.']" />
<div class="card ls-card"><div class="card-body">
<form method="POST" action="{{ route('instructor.courses.store') }}">@csrf
<div class="row g-3">
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Category</label>
<select name="category_id" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Short description</label><input name="short_description" class="form-control"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"></textarea></div>
<div class="col-md-3"><label class="form-label">Level</label><select name="level" class="form-select"><option value="beginner">beginner</option><option value="intermediate">intermediate</option><option value="advanced">advanced</option></select></div>
<div class="col-md-3"><label class="form-label">Language</label><input name="language" class="form-control" value="en"></div>
<div class="col-md-3"><label class="form-label">Price</label><input name="price" type="number" step="0.01" class="form-control" value="0"></div>
<div class="col-md-3"><label class="form-label">Currency</label><input name="currency" class="form-control" value="INR"></div>
</div>
<button class="btn btn-ls-primary mt-3">Create draft</button>
</form></div></div>
@endsection
