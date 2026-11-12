@props(['title' => 'Module guide', 'items' => []])

@php $guideId = 'guide-'.Str::slug($title).'-'.substr(md5($title.implode('|', $items)), 0, 6); @endphp

<div {{ $attributes->merge(['class' => 'card ls-card ls-guide mb-4']) }}>
    <div class="card-header py-2 border-0">
        <button class="btn btn-link text-decoration-none guide-toggle w-100 text-start d-flex justify-content-between align-items-center p-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $guideId }}"
                aria-expanded="true">
            <span><i class="bi bi-lightbulb-fill text-warning me-2"></i>{{ $title }} <span class="badge bg-mint text-teal ms-1">How to use</span></span>
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div id="{{ $guideId }}" class="collapse show">
        <div class="card-body pt-0">
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <ol class="mb-0 ps-3">
                    @foreach ($items as $item)
                        <li class="mb-1">{{ $item }}</li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
</div>
