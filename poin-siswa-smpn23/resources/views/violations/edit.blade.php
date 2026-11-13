@extends('layouts.app')

@section('title', 'Ubah Pelanggaran')

@section('content')
    <x-page-head title="Ubah pelanggaran" :subtitle="$violation->student?->name . ' · ' . ($violation->category?->name ?? '-')"
        :back="route('violations.show', $violation)" back-label="Detail pelanggaran" />

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info mb-3" role="note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>Kategori dan poin tidak bisa diubah supaya saldo tetap konsisten. Untuk koreksi total, batalkan catatan ini lalu buat yang baru.</div>
            </div>

            <form method="POST" action="{{ route('violations.update', $violation) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <label class="form-label" for="incident_date">Tanggal kejadian</label>
                        <input type="date" name="incident_date" id="incident_date" class="form-control @error('incident_date') is-invalid @enderror"
                            value="{{ old('incident_date', $violation->incident_date->format('Y-m-d')) }}" required>
                        @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Keterangan</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $violation->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Bukti saat ini</label>
                        <div>
                            @if($violation->evidence_path)
                                <a href="{{ asset('storage/'.$violation->evidence_path) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-paperclip" aria-hidden="true"></i> Lihat bukti
                                </a>
                            @else
                                <span class="muted" style="font-size:14px">Tidak ada</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="evidence">Ganti bukti</label>
                        <input type="file" name="evidence" id="evidence" class="form-control @error('evidence') is-invalid @enderror"
                            accept="image/*,.pdf,.doc,.docx">
                        @error('evidence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('violations.show', $violation) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
