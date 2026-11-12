@extends('layouts.app')
@section('title', 'New category')
@section('content')
<x-guide title="Module guide" :items="["Slug is generated from the name.","Add a short description for learners.","Set sort order for display priority."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">New category</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection