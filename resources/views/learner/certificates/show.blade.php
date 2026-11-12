@extends('layouts.app')
@section('title', 'Certificate')
@section('content')
<x-guide title="Module guide" :items="["Verification code proves authenticity.","Course title and issue date are shown.","Keep your learner profile name accurate."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Certificate</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection