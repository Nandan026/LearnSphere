@props(['label', 'value', 'icon' => 'bi-graph-up', 'hint' => null, 'href' => null])

@php
    $tag = $href ? 'a' : 'div';
    $classes = 'card ls-card ls-stat h-100 text-decoration-none text-dark'.($href ? ' ls-card-hover' : '');
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
                <div class="text-muted small text-uppercase fw-semibold">{{ $label }}</div>
                <div class="fs-2 fw-bold text-teal mt-1">{{ $value }}</div>
                @if ($hint)
                    <div class="small text-muted mt-1">{{ $hint }}</div>
                @endif
            </div>
            <span class="ls-stat-icon"><i class="bi {{ $icon }}"></i></span>
        </div>
    </div>
</{{ $tag }}>
