@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('content')
    @php
        $labels = ['admin' => 'Admin', 'kepala_sekolah' => 'Kepala Sekolah', 'guru' => 'Guru'];
        $roleLabel = $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    @endphp

    <x-page-head :title="'Tambah akun '.$roleLabel" subtitle="Lengkapi data akun, lalu tentukan status aktifnya."
        :back="route('users.index', $role)" back-label="Daftar akun" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store', $role) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if($role === 'guru')
                        <div class="col-12">
                            <label class="form-label" for="teacher">Data guru</label>
                            <select name="teacher_id" id="teacher" class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">Buat data guru baru otomatis</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>
                                        {{ $teacher->name }} ({{ $teacher->email ?? 'tanpa email' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih guru yang belum punya akun, atau biarkan kosong untuk membuat data guru baru.</div>
                            @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                            minlength="8" required>
                        <div class="form-text">Minimal 8 karakter.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password_confirmation">Ulangi password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @checked(old('is_active', true))>
                            <label class="form-check-label" for="active">Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('users.index', $role) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan akun
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
