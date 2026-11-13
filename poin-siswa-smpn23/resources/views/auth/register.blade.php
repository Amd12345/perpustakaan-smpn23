@extends('layouts.auth')

@section('title', 'Daftar Guru')

@section('content')
    <h1 class="auth-title">Daftar akun guru</h1>
    <p class="auth-sub">Akun aktif setelah disetujui admin sekolah.</p>

    @if($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
            <div>
                <strong>Ada {{ $errors->count() }} hal yang perlu diperbaiki.</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('register.guru') }}" class="stack-12">
        @csrf

        <div>
            <label class="form-label" for="name">Nama lengkap</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" autocomplete="name" required>
        </div>

        <div class="row g-2">
            <div class="col-7">
                <label class="form-label" for="nip">NIP (opsional)</label>
                <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip') }}" inputmode="numeric">
            </div>
            <div class="col-5">
                <label class="form-label" for="gender">Jenis kelamin</label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="">Pilih</option>
                    <option value="L" @selected(old('gender') == 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender') == 'P')>Perempuan</option>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label" for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" inputmode="email" autocomplete="username" required>
        </div>

        <div>
            <label class="form-label" for="phone">Nomor WhatsApp</label>
            <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" inputmode="numeric" placeholder="08xxxxxxxxxx" required>
            <div class="form-text">Dipakai untuk notifikasi poin.</div>
        </div>

        <div class="row g-2">
            <div class="col-6">
                <label class="form-label" for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="col-6">
                <label class="form-label" for="password_confirmation">Ulangi password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Daftar sebagai guru</button>
    </form>

    <p class="text-center small mt-3 mb-0">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
    </p>
@endsection
