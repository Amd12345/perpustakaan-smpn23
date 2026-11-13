@extends('layouts.app')

@section('title', 'Data Prestasi')

@section('content')
    <x-page-head title="Data prestasi" :subtitle="'Tahun ajaran '.($year?->name ?? '-')">
        @can('input-achievement')
            <a href="{{ route('achievements.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Catat prestasi
            </a>
        @endcan
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted" style="font-size:13px">{{ $achievements->total() }} catatan</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#achievementFilter" aria-expanded="false" aria-controls="achievementFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="achievementFilter">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="visually-hidden" for="a-search">Cari siswa</label>
                        <input type="search" id="a-search" name="search" class="form-control" placeholder="Cari siswa / NISN"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="a-category">Kategori</label>
                        <select name="category_id" id="a-category" class="form-select">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="a-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="a-year" class="form-select">
                            <option value="">Semua tahun</option>
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(request('academic_year_id') == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
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
                            <th>Tingkat</th>
                            <th>Tanggal</th>
                            <th>Pencatat</th>
                            <th class="text-end">Poin</th>
                            <th>Publik</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($achievements as $item)
                            <tr>
                                <td class="cell-main" data-label="Siswa">
                                    <a href="{{ route('achievements.show', $item) }}" class="link-stretch">{{ $item->student?->name ?? '-' }}</a>
                                    <span class="list-row-sub d-md-none">{{ $item->student?->class?->name }}</span>
                                </td>
                                <td data-label="Kategori">{{ $item->category?->name }}</td>
                                <td data-label="Tingkat">{{ ucfirst($item->category?->level ?? '-') }}</td>
                                <td data-label="Tanggal">{{ $item->achievement_date->format('d M Y') }}</td>
                                <td data-label="Pencatat">{{ $item->teacher?->name ?? '-' }}</td>
                                <td data-label="Poin" class="text-end text-prestasi num">+{{ $item->point_amount }}</td>
                                <td data-label="Publik">
                                    @if($item->is_public)
                                        <span class="tag tag-success">Tampil</span>
                                    @else
                                        <span class="tag tag-neutral">Internal</span>
                                    @endif
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('achievements.show', $item) }}" class="btn-icon" aria-label="Lihat detail prestasi {{ $item->student?->name }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="8" class="cell-empty">
                                    <x-empty-state icon="award" title="Belum ada catatan" text="Prestasi yang dicatat guru akan muncul di sini.">
                                        @can('input-achievement')
                                            <a href="{{ route('achievements.create') }}" class="btn btn-primary">Catat prestasi</a>
                                        @endcan
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $achievements->links() }}
        </div>
    </div>
@endsection
