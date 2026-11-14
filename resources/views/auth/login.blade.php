@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h5 class="mb-1 text-center fw-semibold">Perpustakaan SMPN 23</h5>
                    <p class="text-muted small text-center mb-4">Silakan masuk untuk mengelola data</p>

                    @error('username')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                    @enderror

                    <form method="POST" action="/login">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text"
                                   name="username"
                                   class="form-control"
                                   value="{{ old('username') }}"
                                   placeholder="Masukkan username"
                                   required
                                   autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Masuk
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
