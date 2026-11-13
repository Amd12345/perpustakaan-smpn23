@extends('layouts.app')

@section('title', 'Detail Prestasi')

@section('content')
    <x-page-head title="Detail prestasi" :subtitle="$achievement->student?->name . ' · ' . ($achievement->student?->class?->name ?? '-')"
        :back="route('achievements.index')" back-label="Data prestasi" />

    <div class="grid-3 section-gap">
        <div class="card grid-wide">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-file-text" aria-hidden="true"></i> Catatan</h2>
                <dl class="detail-list">
                    <div class="detail-item"><dt>Siswa</dt><dd>{{ $achievement->student?->name }}</dd></div>
                    <div class="detail-item"><dt>Kelas</dt><dd>{{ $achievement->student?->class?->name ?? '-' }}</dd></div>
                    <div class="detail-item"><dt>Kategori</dt><dd>{{ $achievement->category?->name }}</dd></div>
                    <div class="detail-item"><dt>Tingkat</dt><dd>{{ ucfirst($achievement->category?->level ?? '-') }}</dd></div>
                    <div class="detail-item"><dt>Tanggal</dt><dd>{{ $achievement->achievement_date->format('d M Y') }}</dd></div>
                    <div class="detail-item"><dt>Poin</dt><dd class="text-prestasi">+{{ $achievement->point_amount }}</dd></div>
                    <div class="detail-item"><dt>Dicatat oleh</dt><dd>{{ $achievement->teacher?->name ?? '-' }}</dd></div>
                    <div class="detail-item">
                        <dt>Publik</dt>
                        <dd>
                            <span class="tag {{ $achievement->is_public ? 'tag-success' : 'tag-neutral' }}">
                                {{ $achievement->is_public ? 'Tampil di dashboard publik' : 'Hanya internal' }}
                            </span>
                        </dd>
                    </div>
                    <div class="detail-item"><dt>Keterangan</dt><dd>{{ $achievement->description }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-image" aria-hidden="true"></i> Dokumentasi</h2>
                @if($achievement->documentation_path)
                    <a href="{{ asset('storage/'.$achievement->documentation_path) }}" target="_blank" rel="noopener">
                        <img src="{{ asset('storage/'.$achievement->documentation_path) }}" class="img-fluid"
                            style="border-radius:10px;border:1px solid var(--line)" loading="lazy"
                            alt="Dokumentasi prestasi {{ $achievement->student?->name }}">
                    </a>
                @else
                    <div class="empty-state py-4">
                        <div class="empty-icon"><i class="bi bi-image" aria-hidden="true"></i></div>
                        <p class="empty-title mb-1">Belum ada dokumentasi</p>
                        <p class="empty-text mb-0">Unggah foto atau sertifikat saat mengubah catatan ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
