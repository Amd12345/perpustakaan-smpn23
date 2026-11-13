@extends('layouts.app')

@section('title', 'Tambah Kategori Prestasi')

@section('content')
    <x-page-head title="Tambah kategori prestasi"
        subtitle="Tingkat dipakai untuk memilah rekap prestasi."
        :back="route('achievement-categories.index')" back-label="Kategori prestasi" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('achievement-categories.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Nama kategori</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="level">Tingkat</label>
                        <select name="level" id="level" class="form-select @error('level') is-invalid @enderror">
                            <option value="">- Pilih -</option>
                            @foreach(['kelas','sekolah','kota','provinsi','nasional','internasional'] as $level)
                                <option value="{{ $level }}" @selected(old('level') == $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3" placeholder="Contoh: juara lomba cerdas cermat antar sekolah">{{ old('description') }}</textarea>
                        <div class="form-text">Keterangan singkat membantu guru memilih kategori yang tepat.</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active"
                                @checked(old('is_active', true))>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                        <div class="form-text">Kategori nonaktif tidak muncul saat mencatat prestasi.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('achievement-categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
