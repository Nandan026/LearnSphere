@extends('layouts.app')
@section('title', 'Live sessions')
@section('content')
<x-guide title="Module guide" :items="["External meeting URL only — no built-in video.","Schedule start/end in your timezone.","Share the join link in the course hub."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Live sessions</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection