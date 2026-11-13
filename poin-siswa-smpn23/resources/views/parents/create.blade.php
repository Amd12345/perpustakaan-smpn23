@extends('layouts.app')

@section('title', 'Tambah Orang Tua')

@section('content')
    <x-page-head title="Tambah orang tua" subtitle="Nomor WhatsApp dipakai untuk menghubungi wali saat ada catatan poin."
        :back="route('parents.index')" back-label="Data orang tua" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('parents.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="parent-name">Nama</label>
                        <input type="text" name="name" id="parent-name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="parent-phone">Nomor WhatsApp</label>
                        <input type="text" name="phone" id="parent-phone"
                            class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="parent-address">Alamat</label>
                        <textarea name="address" id="parent-address" rows="2"
                            class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan orang tua
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
