@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
    <x-page-head :title="$student->name" :back="route('students.index')" back-label="Data siswa">
        @can('manage-master-data')
            <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil" aria-hidden="true"></i> Ubah data
            </a>
        @endcan
    </x-page-head>

    <div class="focus-panel">
        <div class="balance balance-pelanggaran">
            <span class="balance-label">Poin pelanggaran</span>
            <div class="balance-value">{{ number_format($student->violationPoints) }}</div>
            <div class="balance-meta">Saldo yang perlu diselesaikan lewat persyaratan atau konversi.</div>
        </div>
        <div class="balance balance-prestasi">
            <span class="balance-label">Poin prestasi</span>
            <div class="balance-value">{{ number_format($student->achievementPoints) }}</div>
            <div class="balance-meta">Dapat dikonversi untuk mengurangi poin pelanggaran.</div>
        </div>
    </div>

    <div class="grid-3 section-gap">
        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-person-vcard" aria-hidden="true"></i> Profil</h2>
                <dl class="detail-list">
                    <div class="detail-item"><dt>NISN</dt><dd class="num">{{ $student->nisn }}</dd></div>
                    <div class="detail-item"><dt>Kelas</dt><dd>{{ $student->class?->name ?? '-' }}</dd></div>
                    <div class="detail-item"><dt>Tahun ajaran</dt><dd>{{ $student->academicYear?->name ?? '-' }}</dd></div>
                    <div class="detail-item"><dt>Jenis kelamin</dt><dd>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                    <div class="detail-item"><dt>Tanggal lahir</dt><dd>{{ $student->birth_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div class="detail-item"><dt>Status</dt><dd>{{ ucfirst($student->status) }}</dd></div>
                    <div class="detail-item"><dt>Alamat</dt><dd>{{ $student->address ?? '-' }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-people" aria-hidden="true"></i> Orang tua / wali</h2>
                @forelse($student->orangTuas as $parent)
                    <div class="list-row">
                        <div class="list-row-main">
                            <div class="list-row-title">{{ $parent->name }}</div>
                            <div class="list-row-sub">
                                {{ $parent->pivot->relation }} &middot; {{ $parent->phone }}
                            </div>
                        </div>
                        @if($parent->pivot->is_primary)
                            <span class="tag tag-brand">Utama</span>
                        @endif
                    </div>
                @empty
                    <p class="muted mb-0" style="font-size:14px">Belum ada data orang tua.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-clock-history" aria-hidden="true"></i> Transaksi terakhir</h2>
                @forelse($transactions as $tx)
                    <div class="list-row">
                        <div class="list-row-main">
                            <div class="list-row-title">{{ str_replace('_', ' ', ucfirst($tx->type)) }}</div>
                            <div class="list-row-sub">
                                {{ $tx->transaction_date?->format('d M Y') }}
                                @if($tx->description) &middot; {{ \Illuminate\Support\Str::limit($tx->description, 40) }} @endif
                            </div>
                        </div>
                        <div class="list-row-end {{ $tx->balance_type === 'pelanggaran' ? 'text-pelanggaran' : 'text-prestasi' }}">
                            {{ $tx->points > 0 ? '+'.$tx->points : $tx->points }}
                            <span class="list-row-sub d-block" style="font-weight:500">saldo {{ $tx->balance_after }}</span>
                        </div>
                    </div>
                @empty
                    <p class="muted mb-0" style="font-size:14px">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid-2 section-gap">
        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-exclamation-octagon" aria-hidden="true"></i> Pelanggaran</h2>
                <div class="table-responsive">
                    <table class="table table-stack">
                        <thead>
                            <tr><th>Tanggal</th><th>Kategori</th><th>Pencatat</th><th class="text-end">Poin</th></tr>
                        </thead>
                        <tbody>
                            @forelse($violations as $item)
                                <tr>
                                    <td data-label="Tanggal">{{ $item->incident_date->format('d M Y') }}</td>
                                    <td class="cell-main" data-label="Kategori">{{ $item->category?->name }}</td>
                                    <td data-label="Pencatat">{{ $item->teacher?->name ?? '-' }}</td>
                                    <td data-label="Poin" class="text-end text-pelanggaran num">+{{ $item->point_amount }}</td>
                                </tr>
                            @empty
                                <tr class="row-empty"><td colspan="4" class="cell-empty">Tidak ada pelanggaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $violations->links() }}
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-award" aria-hidden="true"></i> Prestasi</h2>
                <div class="table-responsive">
                    <table class="table table-stack">
                        <thead>
                            <tr><th>Tanggal</th><th>Kategori</th><th>Pencatat</th><th class="text-end">Poin</th></tr>
                        </thead>
                        <tbody>
                            @forelse($achievements as $item)
                                <tr>
                                    <td data-label="Tanggal">{{ $item->achievement_date->format('d M Y') }}</td>
                                    <td class="cell-main" data-label="Kategori">{{ $item->category?->name }}</td>
                                    <td data-label="Pencatat">{{ $item->teacher?->name ?? '-' }}</td>
                                    <td data-label="Poin" class="text-end text-prestasi num">+{{ $item->point_amount }}</td>
                                </tr>
                            @empty
                                <tr class="row-empty"><td colspan="4" class="cell-empty">Tidak ada prestasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $achievements->links() }}
            </div>
        </div>
    </div>
@endsection
