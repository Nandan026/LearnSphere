@extends('layouts.app')
@section('title', 'Quiz attempt')
@section('breadcrumb', 'Assessment')

@section('content')
<div class="ls-page-header">
    <div>
        <h1>{{ $attempt->quiz->title }}</h1>
        <p class="ls-lead">Attempt #{{ $attempt->attempt_number }} · answer every question, then submit.</p>
    </div>
    <div class="text-end">
        <div class="small text-muted">Progress</div>
        <div class="fw-bold" id="quizAnsweredCount">0 / {{ $attempt->quiz->questions->count() }}</div>
    </div>
</div>

<x-guide title="Quiz guide" :items="['Submit before leaving — attempts may be limited.', 'Objective questions auto-grade on submit.', 'Short answers may need instructor review.']" />

<form method="POST" action="{{ route('learner.quizzes.submit', $attempt) }}" id="quizForm" class="card ls-card">
    @csrf
    <div class="card-body">
        @foreach ($attempt->quiz->questions as $index => $question)
            <div class="border rounded-3 p-3 mb-3 quiz-question" data-question>
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <span class="badge bg-mint text-teal">Question {{ $index + 1 }}</span>
                    <span class="badge text-bg-light border text-uppercase">{{ str_replace('_', ' ', $question->question_type) }}</span>
                </div>
                <p class="fw-semibold mb-3">{{ $question->question_text }}</p>

                @if (in_array($question->question_type, ['single_choice', 'true_false'], true))
                    @foreach ($question->options as $option)
                        <div class="form-check p-2 rounded mb-1 border quiz-option">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" id="q{{ $question->id }}_{{ $option->id }}">
                            <label class="form-check-label w-100" for="q{{ $question->id }}_{{ $option->id }}">{{ $option->option_text }}</label>
                        </div>
                    @endforeach
                @elseif ($question->question_type === 'multiple_select')
                    <p class="small text-muted">Select all that apply.</p>
                    @foreach ($question->options as $option)
                        <div class="form-check p-2 rounded mb-1 border quiz-option">
                            <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" id="q{{ $question->id }}_{{ $option->id }}">
                            <label class="form-check-label w-100" for="q{{ $question->id }}_{{ $option->id }}">{{ $option->option_text }}</label>
                        </div>
                    @endforeach
                @else
                    <input type="text" class="form-control" name="answers[{{ $question->id }}]" placeholder="Type your answer">
                @endif
            </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center sticky-bottom bg-white py-3 border-top">
            <span class="small text-muted">Your answers are graded when you submit.</span>
            <button class="btn btn-ls-primary btn-lg" data-ls-confirm="Submit this quiz attempt now?">Submit attempt</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('quizForm');
    const counter = document.getElementById('quizAnsweredCount');
    const total = {{ $attempt->quiz->questions->count() }};

    function updateCount() {
        let answered = 0;
        document.querySelectorAll('[data-question]').forEach((block) => {
            const radios = [...block.querySelectorAll('input[type="radio"]')];
            const checks = [...block.querySelectorAll('input[type="checkbox"]')];
            const text = block.querySelector('input[type="text"]');
            if (radios.length && radios.some((r) => r.checked)) answered++;
            else if (checks.length && checks.some((c) => c.checked)) answered++;
            else if (text && text.value.trim() !== '') answered++;
        });
        if (counter) counter.textContent = `${answered} / ${total}`;
    }

    form?.addEventListener('change', updateCount);
    form?.addEventListener('input', updateCount);
    updateCount();
})();
</script>
@endpush
