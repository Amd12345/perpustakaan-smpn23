@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
    <x-page-head title="Tambah tahun ajaran" subtitle="Nama tahun ajaran dipakai di seluruh laporan dan filter."
        :back="route('academic-years.index')" back-label="Tahun ajaran" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('academic-years.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" name="name" id="name" placeholder="2026/2027"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="start_year">Tahun mulai</label>
                        <input type="number" name="start_year" id="start_year"
                            class="form-control @error('start_year') is-invalid @enderror"
                            value="{{ old('start_year', now()->year) }}" required>
                        @error('start_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="end_year">Tahun selesai</label>
                        <input type="number" name="end_year" id="end_year"
                            class="form-control @error('end_year') is-invalid @enderror"
                            value="{{ old('end_year', now()->year + 1) }}" required>
                        @error('end_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" id="active" class="form-check-input">
                            <label class="form-check-label" for="active">Jadikan tahun ajaran aktif</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan tahun ajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
