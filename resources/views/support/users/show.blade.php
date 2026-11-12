@extends('layouts.app')
@section('title', 'User detail')
@section('content')
<x-guide title="Module guide" :items="["View roles and enrollment history.","Do not edit records from this desk.","Escalate account issues to admins."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">User detail</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection