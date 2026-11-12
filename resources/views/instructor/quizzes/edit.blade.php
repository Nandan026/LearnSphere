@extends('layouts.app')
@section('title', 'Edit quiz')
@section('content')
<x-guide title="Module guide" :items="["Select questions to attach with default marks.","Shuffle and results flags control learner UX.","Save status as published for the player."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Edit quiz</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection