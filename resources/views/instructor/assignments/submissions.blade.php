@extends('layouts.app')
@section('title', 'Submissions')
@section('content')
<x-guide title="Module guide" :items="["Review text and uploaded files.","Award marks up to max_marks.","Feedback is visible to the learner."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Submissions</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection