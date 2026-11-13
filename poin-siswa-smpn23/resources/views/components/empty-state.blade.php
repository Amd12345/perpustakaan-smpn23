@props(['icon' => 'inbox', 'title' => 'Belum ada data', 'text' => null, 'compact' => false])

<div class="empty-state {{ $compact ? 'py-3' : '' }}">
    <div class="empty-icon"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i></div>
    <p class="empty-title mb-1">{{ $title }}</p>
    @if($text)
        <p class="empty-text">{{ $text }}</p>
    @endif
    @if(! $slot->isEmpty())
        <div class="d-flex justify-content-center gap-2 flex-wrap">{{ $slot }}</div>
    @endif
</div>
