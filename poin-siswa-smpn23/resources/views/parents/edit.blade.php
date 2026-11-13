@extends('layouts.app')

@section('title', 'Ubah Data Orang Tua')

@section('content')
    <x-page-head title="Ubah data orang tua" :subtitle="$parent->name"
        :back="route('parents.index')" back-label="Data orang tua" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('parents.update', $parent) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="parent-name">Nama</label>
                        <input type="text" name="name" id="parent-name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $parent->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="parent-phone">Nomor WhatsApp</label>
                        <input type="text" name="phone" id="parent-phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $parent->phone) }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="parent-address">Alamat</label>
                        <textarea name="address" id="parent-address" rows="2"
                            class="form-control @error('address') is-invalid @enderror">{{ old('address', $parent->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card section-gap">
        <div class="card-body">
            <h2 class="card-section-title"><i class="bi bi-people" aria-hidden="true"></i> Anak terhubung</h2>
            @forelse($parent->students as $student)
                <div class="list-row">
                    <div class="list-row-main">
                        <div class="list-row-title">{{ $student->name }}</div>
                        <div class="list-row-sub">{{ $student->class?->name ?? 'Kelas belum ditetapkan' }}</div>
                    </div>
                </div>
            @empty
                <x-empty-state icon="person-lines-fill" title="Belum terhubung dengan siswa"
                    text="Hubungkan siswa dari halaman data siswa agar kontak orang tua aktif." compact />
            @endforelse
        </div>
    </div>
@endsection
