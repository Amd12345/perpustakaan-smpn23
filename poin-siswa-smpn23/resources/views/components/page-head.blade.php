@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Kembali'])

<div class="page-head">
    <div class="min-w-0">
        @if($back)
            <a class="page-back" href="{{ $back }}">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> {{ $backLabel }}
            </a>
        @endif
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-sub">{{ $subtitle }}</p>
        @endif
    </div>

    @if(! $slot->isEmpty())
        <div class="page-actions">{{ $slot }}</div>
    @endif
</div>
