@extends('layouts.app')

@section('title', 'Detail Persyaratan')

@section('content')
    @php
        $tagFor = fn (?string $status) => match ($status) {
            'disetujui' => 'tag-success',
            'ditolak' => 'tag-danger',
            'menunggu_verifikasi' => 'tag-accent',
            'revisi' => 'tag-brand',
            default => 'tag-neutral',
        };
    @endphp

    <x-page-head title="Detail persyaratan"
        :subtitle="$task->violation?->student?->name . ' · ' . ($task->violation?->category?->name ?? '-')"
        :back="route('improvements.index')" back-label="Pengurangan poin">
        @if($task->violation)
            <a href="{{ route('violations.show', $task->violation) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-text" aria-hidden="true"></i> Lihat pelanggaran
            </a>
        @endif
    </x-page-head>

    <div class="row g-3 section-gap">
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-clipboard-check" aria-hidden="true"></i> Rincian persyaratan</h2>
                    <dl class="detail-list">
                        <div class="detail-item">
                            <dt>Status</dt>
                            <dd><span class="tag {{ $tagFor($task->status) }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></dd>
                        </div>
                        <div class="detail-item"><dt>Deskripsi</dt><dd>{{ $task->description }}</dd></div>
                        <div class="detail-item"><dt>Maksimal pengurangan</dt><dd class="num">{{ $task->max_point_reduction }} poin</dd></div>
                        <div class="detail-item"><dt>Batas waktu</dt><dd>{{ $task->due_date?->format('d M Y') ?? '-' }}</dd></div>
                        <div class="detail-item"><dt>Dibuat oleh</dt><dd>{{ $task->teacher?->name ?? '-' }}</dd></div>
                        @if($task->status === 'disetujui')
                            <div class="detail-item"><dt>Disetujui oleh</dt><dd>{{ $task->approvedBy?->name ?? '-' }}</dd></div>
                            <div class="detail-item"><dt>Tanggal persetujuan</dt><dd>{{ $task->approved_at?->format('d M Y H:i') }}</dd></div>
                        @endif
                        @if($task->rejection_reason)
                            <div class="detail-item"><dt>Catatan atau alasan</dt><dd>{{ $task->rejection_reason }}</dd></div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-check2-square" aria-hidden="true"></i> Tindakan</h2>

                    @if(in_array($task->status, ['menunggu', 'revisi', 'ditolak']))
                        <form method="POST" action="{{ route('improvements.submissions.store', $task) }}"
                            enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <label class="form-label" for="submission-evidence">Unggah bukti penyelesaian</label>
                            <input type="file" name="evidence" id="submission-evidence"
                                class="form-control @error('evidence') is-invalid @enderror" accept="image/*,.pdf,.doc,.docx" required>
                            <div class="form-text">Foto, PDF, atau dokumen pendukung.</div>
                            @error('evidence')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            <label class="form-label mt-3" for="submission-note">Catatan (opsional)</label>
                            <textarea name="note" id="submission-note" class="form-control" rows="2"
                                placeholder="Keterangan singkat">{{ old('note') }}</textarea>

                            <button type="submit" class="btn btn-primary w-100 mt-3" data-confirm="Unggah bukti dan minta verifikasi?">
                                <i class="bi bi-upload" aria-hidden="true"></i> Unggah dan minta verifikasi
                            </button>
                        </form>
                    @endif

                    @if($task->status === 'menunggu_verifikasi')
                        <form method="POST" action="{{ route('improvements.approve', $task) }}" class="stack-12 mb-3">
                            @csrf
                            <div>
                                <label class="form-label" for="approve-points">Setujui pengurangan poin</label>
                                <input type="number" name="points" id="approve-points"
                                    class="form-control @error('points') is-invalid @enderror" min="0"
                                    max="{{ $task->max_point_reduction }}" value="{{ $task->max_point_reduction }}" required>
                                <div class="form-text">Tidak melebihi {{ $task->max_point_reduction }} poin.</div>
                                @error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100" data-confirm="Setujui dan catat pengurangan poin ke ledger?">
                                <i class="bi bi-check-lg" aria-hidden="true"></i> Setujui
                            </button>
                        </form>

                        <hr class="my-3">

                        <form method="POST" action="{{ route('improvements.reject', $task) }}" class="stack-12 mb-3">
                            @csrf
                            <div>
                                <label class="form-label" for="rejection-reason">Tolak bukti</label>
                                <textarea name="rejection_reason" id="rejection-reason"
                                    class="form-control @error('rejection_reason') is-invalid @enderror" rows="2" required
                                    placeholder="Alasan penolakan">{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-danger w-100" data-confirm="Tolak bukti dan kirim notifikasi?">
                                <i class="bi bi-x-lg" aria-hidden="true"></i> Tolak
                            </button>
                        </form>

                        <hr class="my-3">

                        <form method="POST" action="{{ route('improvements.revision', $task) }}" class="stack-12">
                            @csrf
                            <div>
                                <label class="form-label" for="revision-note">Minta revisi</label>
                                <textarea name="note" id="revision-note"
                                    class="form-control @error('note') is-invalid @enderror" rows="2" required
                                    placeholder="Perbaikan yang diminta">{{ old('note') }}</textarea>
                                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Minta revisi
                            </button>
                        </form>
                    @endif

                    @if($task->status === 'disetujui')
                        <div class="alert alert-success mb-0" role="status">
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            <div>Persyaratan sudah disetujui dan poin sudah dikurangkan.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="card-section-title"><i class="bi bi-paperclip" aria-hidden="true"></i> Riwayat bukti</h2>

            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Bukti</th>
                            <th>Catatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($task->submissions as $submission)
                            <tr>
                                <td class="num" data-label="Tanggal">{{ $submission->created_at->format('d M Y H:i') }}</td>
                                <td data-label="Bukti">
                                    <a href="{{ asset('storage/'.$submission->evidence_path) }}" target="_blank" rel="noopener"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-paperclip" aria-hidden="true"></i> Buka bukti
                                    </a>
                                </td>
                                <td data-label="Catatan">{{ $submission->note ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $tagFor($submission->status) }}">{{ ucfirst($submission->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="4" class="cell-empty">
                                    <x-empty-state icon="paperclip" title="Belum ada bukti"
                                        text="Bukti yang diunggah guru akan muncul di sini untuk diverifikasi." compact />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
