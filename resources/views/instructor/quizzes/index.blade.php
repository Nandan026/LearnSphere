@extends('layouts.app')
@section('title', 'Quizzes')
@section('content')
<x-guide title="Module guide" :items="["Attach questions from banks on the edit screen.","Set pass percentage and attempt limits.","Publish when learners should see the quiz."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Quizzes</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection