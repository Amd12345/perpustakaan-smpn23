@extends('layouts.app')

@section('title', 'Ubah Kategori Prestasi')

@section('content')
    <x-page-head title="Ubah kategori prestasi" :subtitle="$category->name"
        :back="route('achievement-categories.index')" back-label="Kategori prestasi" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('achievement-categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Nama kategori</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="level">Tingkat</label>
                        <select name="level" id="level" class="form-select @error('level') is-invalid @enderror">
                            <option value="">- Pilih -</option>
                            @foreach(['kelas','sekolah','kota','provinsi','nasional','internasional'] as $level)
                                <option value="{{ $level }}" @selected(old('level', $category->level) == $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                        @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3">{{ old('description', $category->description) }}</textarea>
                        <div class="form-text">Keterangan singkat membantu guru memilih kategori yang tepat.</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active"
                                @checked(old('is_active', $category->is_active))>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                        <div class="form-text">Kategori nonaktif tidak muncul saat mencatat prestasi.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('achievement-categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
