@extends('layouts.guest')

@section('title', 'Verify certificate')

@section('content')
<x-guide title="Verification guide" :items="[
    'Enter the certificate code from the learner credential (format LS-XXXXXX).',
    'Successful verification shows learner name and course title.',
    'Invalid codes return a clear not-found message.',
]" />

<div class="card ls-card">
    <div class="card-body">
        <h1 class="h4 mb-3">Certificate verification</h1>
        <form method="POST" action="{{ route('certificates.verify.submit') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-8">
                <label class="form-label">Certificate code</label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ $code ?? '' }}" required>
            </div>
            <div class="col-md-4">
                <button class="btn btn-ls-primary w-100">Verify</button>
            </div>
        </form>

        @isset($verified)
            @if ($verified && $certificate)
                <div class="alert alert-success mt-4 mb-0">
                    <strong>Valid certificate</strong><br>
                    Learner: {{ $certificate->user->name }}<br>
                    Course: {{ $certificate->course->title }}<br>
                    Issued: {{ $certificate->issued_at?->format('M j, Y') }}
                </div>
            @else
                <div class="alert alert-warning mt-4 mb-0">No certificate found for that code.</div>
            @endif
        @endisset
    </div>
</div>
@endsection
