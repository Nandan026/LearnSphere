@extends('layouts.app')
@section('title', 'Order history')
@section('content')
<x-guide title="Module guide" :items="["Simulated payments only — no real cards.","Paid orders should grant enrollment access.","Contact support if status looks wrong."]" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">Order history</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection