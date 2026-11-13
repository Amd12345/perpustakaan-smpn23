@extends('layouts.app')

@section('title', 'Pengurangan Poin')

@section('content')
    <x-page-head title="Pengurangan poin" subtitle="Persyaratan penyelesaian pelanggaran">
        <a href="{{ route('conversions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-right" aria-hidden="true"></i> Konversi poin
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted" style="font-size:13px">{{ $tasks->total() }} persyaratan</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#improvementFilter" aria-expanded="false" aria-controls="improvementFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="improvementFilter">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="visually-hidden" for="i-status">Status persyaratan</label>
                        <select name="status" id="i-status" class="form-select">
                            <option value="">Semua status</option>
                            @foreach(['menunggu','menunggu_verifikasi','disetujui','ditolak','revisi'] as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Pelanggaran</th>
                            <th class="text-end">Poin</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            @php
                                $statusTag = match ($task->status) {
                                    'disetujui' => 'tag-success',
                                    'ditolak' => 'tag-danger',
                                    'menunggu_verifikasi' => 'tag-accent',
                                    'revisi' => 'tag-brand',
                                    default => 'tag-neutral',
                                };
                            @endphp
                            <tr>
                                <td class="cell-main" data-label="Siswa">
                                    {{ $task->violation?->student?->name ?? '-' }}
                                    <div class="list-row-sub">{{ $task->violation?->student?->class?->name ?? '-' }}</div>
                                </td>
                                <td data-label="Pelanggaran">
                                    {{ $task->violation?->category?->name ?? '-' }}
                                    <div class="list-row-sub">{{ $task->violation?->incident_date?->format('d M Y') ?? '-' }}</div>
                                </td>
                                <td data-label="Poin" class="text-end num fw-semibold">{{ $task->max_point_reduction }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $statusTag }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('improvements.show', $task) }}" class="btn-icon"
                                        aria-label="Lihat persyaratan {{ $task->violation?->student?->name }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="clipboard-check" title="Belum ada persyaratan"
                                        text="Persyaratan dibuat dari halaman detail pelanggaran yang masih aktif." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $tasks->links() }}
        </div>
    </div>
@endsection
