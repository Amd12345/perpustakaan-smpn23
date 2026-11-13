@extends('layouts.app')

@section('title', 'Ubah Tahun Ajaran')

@section('content')
    <x-page-head title="Ubah tahun ajaran {{ $year->name }}" subtitle="Perubahan berlaku untuk data yang memakai tahun ajaran ini."
        :back="route('academic-years.index')" back-label="Tahun ajaran" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('academic-years.update', $year) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $year->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="start_year">Tahun mulai</label>
                        <input type="number" name="start_year" id="start_year"
                            class="form-control @error('start_year') is-invalid @enderror"
                            value="{{ old('start_year', $year->start_year) }}" required>
                        @error('start_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="end_year">Tahun selesai</label>
                        <input type="number" name="end_year" id="end_year"
                            class="form-control @error('end_year') is-invalid @enderror"
                            value="{{ old('end_year', $year->end_year) }}" required>
                        @error('end_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" id="active"
                                class="form-check-input" @checked(old('is_active', $year->is_active))>
                            <label class="form-check-label" for="active">Tahun ajaran aktif</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
