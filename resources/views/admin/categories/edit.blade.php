@extends('layouts.app')
@section('title', 'Edit category')
@section('content')
<x-guide title="Module guide" :items="["Renaming updates the slug.","Toggle status instead of deleting when courses exist.","Keep sort order consistent."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Edit category</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection