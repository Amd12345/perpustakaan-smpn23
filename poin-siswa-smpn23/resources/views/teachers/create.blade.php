@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
    <x-page-head title="Tambah guru" subtitle="Lengkapi data guru. NIP boleh dikosongkan bila belum ada."
        :back="route('teachers.index')" back-label="Data guru" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('teachers.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="teacher-name">Nama lengkap</label>
                        <input type="text" name="name" id="teacher-name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="teacher-nip">NIP</label>
                        <input type="text" name="nip" id="teacher-nip"
                            class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}">
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label" for="teacher-gender">Jenis kelamin</label>
                        <select name="gender" id="teacher-gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Pilih</option>
                            <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="teacher-email">Email</label>
                        <input type="email" name="email" id="teacher-email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="teacher-phone">Telepon</label>
                        <input type="text" name="phone" id="teacher-phone"
                            class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="teacher-address">Alamat</label>
                        <textarea name="address" id="teacher-address" rows="2"
                            class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan guru
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
