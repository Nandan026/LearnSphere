@extends('layouts.app')
@section('title', 'Question banks')
@section('content')
<x-guide title="Module guide" :items="["Banks are scoped to this course.","Add questions before attaching to quizzes.","Delete unused banks to reduce clutter."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Question banks</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection