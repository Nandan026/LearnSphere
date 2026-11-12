@extends('layouts.app')
@section('title', 'Audit log')
@section('content')
<x-guide title="Module guide" :items="["Each row is an immutable admin action.","Use entity id to trace related records.","IP and agent aid security reviews."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Audit log</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection