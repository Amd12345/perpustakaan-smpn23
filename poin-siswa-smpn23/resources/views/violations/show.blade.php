@extends('layouts.app')

@section('title', 'Detail Pelanggaran')

@section('content')
    <x-page-head title="Detail pelanggaran" :subtitle="$violation->student?->name . ' · ' . ($violation->student?->class?->name ?? '-')"
        :back="route('violations.index')" back-label="Data pelanggaran">
        @can('manage-improvement')
            @if(! $violation->improvementTask && $violation->status === 'aktif')
                <a href="{{ route('improvements.create', ['violation' => $violation->id]) }}" class="btn btn-primary">
                    <i class="bi bi-list-check" aria-hidden="true"></i> Buat persyaratan
                </a>
            @endif
        @endcan
    </x-page-head>

    <div class="grid-3 section-gap">
        <div class="card grid-wide">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-file-text" aria-hidden="true"></i> Catatan</h2>
                <dl class="detail-list">
                    <div class="detail-item"><dt>Siswa</dt><dd>{{ $violation->student?->name }}</dd></div>
                    <div class="detail-item"><dt>Kelas</dt><dd>{{ $violation->student?->class?->name ?? '-' }}</dd></div>
                    <div class="detail-item"><dt>Kategori</dt><dd>{{ $violation->category?->name }}</dd></div>
                    <div class="detail-item"><dt>Tanggal</dt><dd>{{ $violation->incident_date->format('d M Y') }}</dd></div>
                    <div class="detail-item"><dt>Poin</dt><dd class="text-pelanggaran">+{{ $violation->point_amount }}</dd></div>
                    <div class="detail-item"><dt>Dicatat oleh</dt><dd>{{ $violation->teacher?->name ?? '-' }}</dd></div>
                    <div class="detail-item">
                        <dt>Status</dt>
                        <dd><span class="tag {{ $violation->status === 'aktif' ? 'tag-success' : 'tag-neutral' }}">{{ ucfirst($violation->status) }}</span></dd>
                    </div>
                    <div class="detail-item"><dt>Keterangan</dt><dd>{{ $violation->description }}</dd></div>
                </dl>

                @if($violation->evidence_path)
                    <h3 class="card-section-title mt-3"><i class="bi bi-image" aria-hidden="true"></i> Bukti</h3>
                    <a href="{{ asset('storage/'.$violation->evidence_path) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/'.$violation->evidence_path) }}" class="img-fluid"
                            style="max-height:320px;border-radius:10px;border:1px solid var(--line)" loading="lazy"
                            alt="Bukti pelanggaran {{ $violation->student?->name }}">
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-check2-square" aria-hidden="true"></i> Persyaratan penyelesaian</h2>
                @if($violation->improvementTask)
                    @php $task = $violation->improvementTask; @endphp
                    <div class="mb-2">
                        <span class="tag {{ $task->status === 'disetujui' ? 'tag-success' : ($task->status === 'ditolak' ? 'tag-danger' : 'tag-accent') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                    <p style="font-size:14px">{{ $task->description }}</p>
                    <dl class="detail-list">
                        <div class="detail-item"><dt>Maks. pengurangan</dt><dd>{{ $task->max_point_reduction }} poin</dd></div>
                        <div class="detail-item"><dt>Batas waktu</dt><dd>{{ $task->due_date?->format('d M Y') ?? '-' }}</dd></div>
                    </dl>
                    <a href="{{ route('improvements.show', $task) }}" class="btn btn-outline-secondary w-100 mt-3">
                        <i class="bi bi-eye" aria-hidden="true"></i> Lihat persyaratan
                    </a>
                @else
                    <p class="muted mb-0" style="font-size:14px">Belum ada persyaratan penyelesaian.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
