@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h5 class="mb-4 fw-semibold">Pengaturan Akun</h5>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('account.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text"
                                   name="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $user->username) }}"
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Username dipakai untuk masuk.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <p class="text-muted small mb-3">Kosongkan password baru jika tidak ingin mengubah password.</p>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Diisi hanya jika mengubah password.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
