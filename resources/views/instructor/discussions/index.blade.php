@extends('layouts.app')
@section('title', 'Discussion moderation')
@section('content')
<x-guide title="Module guide" :items="["Threads scoped to courses you teach.","Super admins see all courses.","Pin or moderate inappropriate posts via status."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Discussion moderation</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection