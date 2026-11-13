@extends('layouts.app')

@section('title', 'Ubah Akun')

@section('content')
    @php
        $labels = ['admin' => 'Admin', 'kepala_sekolah' => 'Kepala Sekolah', 'guru' => 'Guru'];
        $roleLabel = $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    @endphp

    <x-page-head :title="'Ubah akun '.$roleLabel" :subtitle="'Akun '.$roleLabel.' · '.$user->email"
        :back="route('users.index', $role)" back-label="Daftar akun" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', [$role, $user]) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password">Password baru (opsional)</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                            minlength="8">
                        <div class="form-text">Biarkan kosong jika password tidak diubah. Minimal 8 karakter.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password_confirmation">Ulangi password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', $user->is_active))>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                        <div class="form-text">Akun nonaktif tidak bisa dipakai masuk, tetapi catatannya tetap tersimpan.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('users.index', $role) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
