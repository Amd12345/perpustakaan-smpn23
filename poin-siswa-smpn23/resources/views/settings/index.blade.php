@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <x-page-head title="Pengaturan sistem" subtitle="Identitas sekolah, notifikasi WhatsApp, dan konversi poin." />

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        <div class="grid-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title">
                        <i class="bi bi-building" aria-hidden="true"></i> Identitas sekolah
                    </h2>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="school_name">Nama sekolah</label>
                            <input type="text" name="school_name" id="school_name"
                                class="form-control @error('school_name') is-invalid @enderror"
                                value="{{ old('school_name', $schoolName) }}" required>
                            @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="school_address">Alamat</label>
                            <textarea name="school_address" id="school_address" rows="2"
                                class="form-control @error('school_address') is-invalid @enderror">{{ old('school_address', $schoolAddress) }}</textarea>
                            @error('school_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="school_city">Kota</label>
                            <input type="text" name="school_city" id="school_city"
                                class="form-control @error('school_city') is-invalid @enderror"
                                value="{{ old('school_city', $schoolCity) }}">
                            @error('school_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="school_phone">Telepon</label>
                            <input type="text" name="school_phone" id="school_phone"
                                class="form-control @error('school_phone') is-invalid @enderror"
                                value="{{ old('school_phone', $schoolPhone) }}">
                            @error('school_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="school_email">Email</label>
                            <input type="email" name="school_email" id="school_email"
                                class="form-control @error('school_email') is-invalid @enderror"
                                value="{{ old('school_email', $schoolEmail) }}">
                            @error('school_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="stack-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-section-title">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i> Notifikasi WhatsApp
                        </h2>

                        <div class="alert {{ $fonnteKeyConfigured ? 'alert-success' : 'alert-warning' }} mb-3">
                            <i class="bi bi-{{ $fonnteKeyConfigured ? 'check-circle' : 'exclamation-triangle' }}" aria-hidden="true"></i>
                            <div>
                                Token Fonnte <strong>{{ $fonnteKeyConfigured ? 'sudah diatur di environment' : 'belum diatur' }}</strong>.
                                Isi FONTE_API_KEY pada berkas .env.
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="wa_threshold">Ambang poin pelanggaran</label>
                                <input type="number" name="wa_threshold" id="wa_threshold" min="1"
                                    class="form-control @error('wa_threshold') is-invalid @enderror"
                                    value="{{ old('wa_threshold', $waThreshold) }}" required>
                                <div class="form-text">Notifikasi dikirim setiap kali total poin pelanggaran melampaui kelipatan angka ini.</div>
                                @error('wa_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="wa_achievement_enabled" value="1" id="waAchievement"
                                        class="form-check-input" @checked(old('wa_achievement_enabled', $waAchievementEnabled))>
                                    <label class="form-check-label" for="waAchievement">Kirim notifikasi setiap prestasi dicatat</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="card-section-title">
                            <i class="bi bi-arrow-left-right" aria-hidden="true"></i> Konversi poin
                        </h2>

                        <label class="form-label" for="point_conversion_ratio">Rasio konversi</label>
                        <input type="number" name="point_conversion_ratio" id="point_conversion_ratio" min="1"
                            class="form-control @error('point_conversion_ratio') is-invalid @enderror"
                            value="{{ old('point_conversion_ratio', $conversionRatio) }}" required>
                        <div class="form-text">Satu poin prestasi setara dengan sekian poin pengurangan pelanggaran.</div>
                        @error('point_conversion_ratio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save" aria-hidden="true"></i> Simpan pengaturan
            </button>
        </div>
    </form>
@endsection
