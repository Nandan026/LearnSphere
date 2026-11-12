@extends('layouts.app')
@section('title', 'Assignments')
@section('content')
<x-guide title="Module guide" :items="["Set due dates and max marks.","Open submissions list to grade work.","Allow late submission when appropriate."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Assignments</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection