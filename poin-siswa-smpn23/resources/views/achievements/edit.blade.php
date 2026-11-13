@extends('layouts.app')

@section('title', 'Ubah Prestasi')

@section('content')
    <x-page-head title="Ubah prestasi" :subtitle="$achievement->student?->name . ' · ' . ($achievement->category?->name ?? '-')"
        :back="route('achievements.show', $achievement)" back-label="Detail prestasi" />

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info mb-3" role="note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>Kategori dan poin tidak bisa diubah supaya saldo tetap konsisten.</div>
            </div>

            <form method="POST" action="{{ route('achievements.update', $achievement) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <label class="form-label" for="achievement_date">Tanggal prestasi</label>
                        <input type="date" name="achievement_date" id="achievement_date" class="form-control @error('achievement_date') is-invalid @enderror"
                            value="{{ old('achievement_date', $achievement->achievement_date->format('Y-m-d')) }}" required>
                        @error('achievement_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Keterangan</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $achievement->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Dokumentasi saat ini</label>
                        <div>
                            @if($achievement->documentation_path)
                                <a href="{{ asset('storage/'.$achievement->documentation_path) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-paperclip" aria-hidden="true"></i> Lihat dokumentasi
                                </a>
                            @else
                                <span class="muted" style="font-size:14px">Tidak ada</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="documentation">Ganti dokumentasi</label>
                        <input type="file" name="documentation" id="documentation" class="form-control @error('documentation') is-invalid @enderror"
                            accept="image/*,.pdf,.doc,.docx">
                        @error('documentation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_public" value="1" class="form-check-input" id="public" @checked(old('is_public', $achievement->is_public))>
                            <label class="form-check-label" for="public">Tampilkan di dashboard publik</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('achievements.show', $achievement) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
