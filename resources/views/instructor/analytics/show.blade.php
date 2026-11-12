@extends('layouts.app')
@section('title', 'Course analytics')
@section('content')
<x-guide title="Module guide" :items="["Enrollment count includes active seats.","Completions require all published lessons done.","Use insights to improve engagement."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Course analytics</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection