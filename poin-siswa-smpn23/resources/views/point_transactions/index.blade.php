@extends('layouts.app')

@section('title', 'Riwayat Poin')

@section('content')
    <x-page-head title="Riwayat transaksi poin" :subtitle="'Tahun ajaran '.($year?->name ?? '-')">
        <span class="muted" style="font-size:13px">{{ $transactions->total() }} transaksi</span>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="filter-bar">
                <div class="row g-2">
                    <div class="col-12 col-md-3">
                        <label class="visually-hidden" for="t-search">Cari siswa</label>
                        <input type="search" id="t-search" name="search" class="form-control" placeholder="Cari siswa / NISN"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="visually-hidden" for="t-type">Jenis</label>
                        <select name="type" id="t-type" class="form-select">
                            <option value="">Semua jenis</option>
                            @foreach(['pelanggaran', 'prestasi', 'pengurangan', 'konversi', 'konversi_prestasi', 'penyesuaian'] as $type)
                                <option value="{{ $type }}" @selected(request('type') == $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="visually-hidden" for="t-balance">Saldo</label>
                        <select name="balance_type" id="t-balance" class="form-select">
                            <option value="">Semua saldo</option>
                            <option value="pelanggaran" @selected(request('balance_type') == 'pelanggaran')>Pelanggaran</option>
                            <option value="prestasi" @selected(request('balance_type') == 'prestasi')>Prestasi</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="visually-hidden" for="t-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="t-year" class="form-select">
                            <option value="">Semua tahun</option>
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(request('academic_year_id') == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Saldo</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Poin</th>
                            <th class="text-end">Saldo akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $item)
                            <tr>
                                <td class="cell-main" data-label="Siswa">
                                    {{ $item->student?->name ?? '-' }}
                                    <span class="list-row-sub d-md-none">{{ $item->student?->class?->name }}</span>
                                </td>
                                <td data-label="Tanggal">{{ $item->transaction_date?->format('d M Y H:i') }}</td>
                                <td data-label="Jenis">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</td>
                                <td data-label="Saldo">
                                    <span class="tag {{ $item->balance_type === 'pelanggaran' ? 'tag-danger' : 'tag-success' }}">
                                        {{ ucfirst($item->balance_type) }}
                                    </span>
                                </td>
                                <td data-label="Deskripsi">{{ $item->description ?? '-' }}</td>
                                <td data-label="Poin" class="text-end num {{ $item->points < 0 ? 'text-prestasi' : 'text-pelanggaran' }}">
                                    {{ $item->points > 0 ? '+'.$item->points : $item->points }}
                                </td>
                                <td data-label="Saldo akhir" class="text-end num">{{ $item->balance_after }}</td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="7" class="cell-empty">
                                    <x-empty-state icon="clock-history" title="Belum ada transaksi" text="Setiap perubahan poin akan tercatat di sini, termasuk pengurangan dan konversi." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transactions->links() }}
        </div>
    </div>
@endsection
