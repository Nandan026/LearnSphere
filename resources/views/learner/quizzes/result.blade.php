@extends('layouts.app')
@section('title', 'Quiz result')
@section('content')
<x-guide title="Results guide" :items="['Pass/fail follows quiz pass percentage.','Review scores when show_results is enabled.','Retake only if attempts remain.']" />
<div class="card ls-card"><div class="card-body">
<h1 class="h5">{{ $attempt->quiz->title }} — Attempt #{{ $attempt->attempt_number }}</h1>
<p>Score: {{ $attempt->score }} / {{ $attempt->max_score }} ({{ $attempt->percentage }}%)</p>
<p class="{{ $attempt->passed ? 'text-success' : 'text-danger' }}">{{ $attempt->passed ? 'Passed' : 'Not passed' }}</p>
@if ($attempt->quiz->show_results)
<ul class="small">@foreach($attempt->answers as $ans)<li>Q{{ $ans->question_id }}: {{ $ans->marks_awarded }} marks</li>@endforeach</ul>
@endif
</div></div>
@endsection
