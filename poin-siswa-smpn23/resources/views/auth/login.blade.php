@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <h1 class="auth-title">Masuk ke akun</h1>
    <p class="auth-sub">Pakai email sekolah yang sudah terdaftar.</p>

    @if(session('status'))
        <div class="alert alert-info mb-3" role="status">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="stack-12">
        @csrf

        <div>
            <label class="form-label" for="email">Email</label>
            <input type="email" name="email" id="email" inputmode="email" autocomplete="username"
                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" id="password" autocomplete="current-password"
                class="form-control @error('password') is-invalid @enderror" required>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Masuk</button>
    </form>

    <p class="text-center small mt-3 mb-0">
        Guru belum punya akun? <a href="{{ route('register.guru') }}">Daftar sebagai guru</a>
    </p>
@endsection
