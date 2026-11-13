@extends('layouts.app')

@section('title', 'Buat Persyaratan')

@section('content')
    <x-page-head title="Buat persyaratan"
        :subtitle="$violation->student?->name . ' · ' . ($violation->category?->name ?? '-') . ' (' . $violation->point_amount . ' poin)'"
        :back="route('violations.show', $violation)" back-label="Detail pelanggaran" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('improvements.store') }}">
                @csrf
                <input type="hidden" name="violation_id" value="{{ $violation->id }}">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="description">Deskripsi persyaratan</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                            rows="4" required placeholder="Tuliskan hal yang harus dilakukan siswa atau orang tua">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label" for="max_point_reduction">Maksimal poin dikurangi</label>
                        <input type="number" name="max_point_reduction" id="max_point_reduction"
                            class="form-control @error('max_point_reduction') is-invalid @enderror" min="1"
                            max="{{ $violation->point_amount }}" value="{{ old('max_point_reduction', $violation->point_amount) }}" required>
                        <div class="form-text">Tidak melebihi {{ $violation->point_amount }} poin pelanggaran.</div>
                        @error('max_point_reduction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label" for="due_date">Batas waktu (opsional)</label>
                        <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror"
                            value="{{ old('due_date') }}">
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="point-preview">
                            <span class="muted" style="font-size:13px">Poin pelanggaran</span>
                            <strong class="text-pelanggaran">{{ $violation->point_amount }}</strong>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('violations.show', $violation) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary"
                        data-confirm="Buat persyaratan dan kirim notifikasi WhatsApp ke orang tua?">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan dan kirim notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
