@extends('layouts.app')
@section('title', 'Discussions')
@section('content')
<x-guide title="Discussions guide" :items="['Start a thread or reply to classmates.','Stay on topic for the course community.','Instructors may moderate threads.']" />
<div class="card ls-card mb-3"><div class="card-body">
<h2 class="h6">New thread</h2>
<form method="POST" action="{{ route('learner.discussions.store', $course) }}">@csrf
<input name="title" class="form-control mb-2" placeholder="Title" required>
<textarea name="body" class="form-control mb-2" rows="3" required></textarea>
<button class="btn btn-ls-primary btn-sm">Post thread</button>
</form></div></div>
@foreach ($threads as $thread)
<div class="card ls-card mb-2"><div class="card-body">
<h3 class="h6 mb-1">{{ $thread->title }}</h3>
<p class="small text-muted mb-2">by {{ $thread->user?->name }}</p>
<p>{{ $thread->body }}</p>
@foreach ($thread->replies as $reply)
<div class="border-start ps-3 ms-2 small mb-2"><strong>{{ $reply->user?->name }}</strong>: {{ $reply->body }}</div>
@endforeach
<form method="POST" action="{{ route('learner.discussions.reply', [$course, $thread]) }}" class="mt-2">@csrf
<input name="body" class="form-control form-control-sm" placeholder="Reply" required>
<button class="btn btn-outline-secondary btn-sm mt-1">Reply</button>
</form>
</div></div>
@endforeach
@endsection
