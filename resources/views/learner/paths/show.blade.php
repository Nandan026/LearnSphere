@extends('layouts.app')
@section('title', 'Learning path')
@section('content')
<x-guide title="Module guide" :items="["Follow linked courses in sort order.","Enroll in each course individually.","Track progress on your dashboard."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Learning path</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection