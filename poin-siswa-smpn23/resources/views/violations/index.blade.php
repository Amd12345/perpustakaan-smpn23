@extends('layouts.app')

@section('title', 'Data Pelanggaran')

@section('content')
    <x-page-head title="Data pelanggaran" :subtitle="'Tahun ajaran '.($year?->name ?? '-')">
        @can('input-violation')
            <a href="{{ route('violations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Catat pelanggaran
            </a>
        @endcan
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted" style="font-size:13px">{{ $violations->total() }} catatan</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#violationFilter" aria-expanded="false" aria-controls="violationFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="violationFilter">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="visually-hidden" for="v-search">Cari siswa</label>
                        <input type="search" id="v-search" name="search" class="form-control" placeholder="Cari siswa / NISN"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="v-category">Kategori</label>
                        <select name="category_id" id="v-category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="visually-hidden" for="v-status">Status</label>
                        <select name="status" id="v-status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="aktif" @selected(request('status') == 'aktif')>Aktif</option>
                            <option value="dibatalkan" @selected(request('status') == 'dibatalkan')>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="visually-hidden" for="v-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="v-year" class="form-select">
                            <option value="">Semua tahun</option>
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(request('academic_year_id') == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Pencatat</th>
                            <th class="text-end">Poin</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($violations as $item)
                            <tr>
                                <td class="cell-main" data-label="Siswa">
                                    <a href="{{ route('violations.show', $item) }}" class="link-stretch">{{ $item->student?->name ?? '-' }}</a>
                                    <span class="list-row-sub d-md-none">{{ $item->student?->class?->name }}</span>
                                </td>
                                <td data-label="Kategori">{{ $item->category?->name }}</td>
                                <td data-label="Tanggal">{{ $item->incident_date->format('d M Y') }}</td>
                                <td data-label="Pencatat">{{ $item->teacher?->name ?? '-' }}</td>
                                <td data-label="Poin" class="text-end text-pelanggaran num">+{{ $item->point_amount }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $item->status === 'aktif' ? 'tag-success' : 'tag-neutral' }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('violations.show', $item) }}" class="btn-icon" aria-label="Lihat detail pelanggaran {{ $item->student?->name }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="7" class="cell-empty">
                                    <x-empty-state icon="exclamation-octagon" title="Belum ada catatan" text="Pelanggaran yang dicatat guru akan muncul di sini.">
                                        @can('input-violation')
                                            <a href="{{ route('violations.create') }}" class="btn btn-primary">Catat pelanggaran</a>
                                        @endcan
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $violations->links() }}
        </div>
    </div>
@endsection
