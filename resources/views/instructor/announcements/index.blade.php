@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
<x-guide title="Module guide" :items="["Posted announcements notify enrolled learners.","Keep titles concise for notification feeds.","Remove outdated posts when needed."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Announcements</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection