@php
    $messages = [];
    foreach (['status' => 'success', 'success' => 'success', 'error' => 'danger', 'warning' => 'warning'] as $key => $tone) {
        if (session($key)) {
            $messages[] = ['text' => session($key), 'tone' => $tone];
        }
    }
    $iconFor = ['success' => 'check-circle-fill', 'danger' => 'exclamation-octagon-fill', 'warning' => 'exclamation-triangle-fill'];
@endphp

@if($messages)
    <div class="toast-stack" role="status" aria-live="polite">
        @foreach($messages as $message)
            <div class="toast-item {{ $message['tone'] }}" data-toast>
                <i class="bi bi-{{ $iconFor[$message['tone']] ?? 'info-circle-fill' }}" aria-hidden="true"></i>
                <div class="flex-grow-1">{{ $message['text'] }}</div>
                <button class="toast-close" type="button" data-toast-close aria-label="Tutup pemberitahuan">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mb-3" role="alert">
        <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
        <div>
            <strong>Ada {{ $errors->count() }} hal yang perlu diperbaiki.</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
