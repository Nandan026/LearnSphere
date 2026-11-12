@extends('layouts.app')
@section('title', 'Learning paths')
@section('content')
<x-guide title="Module guide" :items="["Paths sequence multiple courses.","Complete courses in suggested order.","Published paths only are listed."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Learning paths</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection