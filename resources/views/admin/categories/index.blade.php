@extends('layouts.app')
@section('title', 'Categories')
@section('content')
<x-guide title="Module guide" :items="["Sort order controls catalogue filters.","Inactive categories hide from new courses.","Delete only empty categories."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Categories</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection