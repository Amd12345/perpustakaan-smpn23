@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <x-page-head title="Data siswa" :subtitle="'Tahun ajaran '.($year?->name ?? '-' )">
        @can('manage-master-data')
            <a href="{{ route('students.import') }}" class="btn btn-outline-secondary">
                <i class="bi bi-upload" aria-hidden="true"></i> Import
            </a>
            <a href="{{ route('students.export') }}" class="btn btn-outline-secondary">
                <i class="bi bi-download" aria-hidden="true"></i> Export
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah siswa
            </a>
        @endcan
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted" style="font-size:13px">{{ $students->total() }} siswa</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#studentFilter" aria-expanded="false" aria-controls="studentFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="studentFilter">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-search">Cari nama atau NISN</label>
                        <input type="search" id="f-search" name="search" class="form-control" placeholder="Cari nama / NISN"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-class">Kelas</label>
                        <select name="class_id" id="f-class" class="form-select">
                            <option value="">Semua kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="visually-hidden" for="f-status">Status</label>
                        <select name="status" id="f-status" class="form-select">
                            <option value="">Semua status</option>
                            @foreach(['aktif', 'nonaktif', 'lulus', 'pindah'] as $status)
                                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="visually-hidden" for="f-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="f-year" class="form-select">
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
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>L/P</th>
                            <th>Status</th>
                            <th class="text-end">Poin pelanggaran</th>
                            <th class="text-end">Poin prestasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $item)
                            <tr>
                                <td class="cell-main" data-label="Nama">
                                    <a href="{{ route('students.show', $item) }}" class="link-stretch">{{ $item->name }}</a>
                                </td>
                                <td data-label="NISN" class="num">{{ $item->nisn }}</td>
                                <td data-label="Kelas">{{ $item->class?->name ?? '-' }}</td>
                                <td data-label="L/P">{{ $item->gender }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $item->status === 'aktif' ? 'tag-success' : 'tag-neutral' }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td data-label="Poin pelanggaran" class="text-end text-pelanggaran num">{{ number_format($item->violation_points ?? 0) }}</td>
                                <td data-label="Poin prestasi" class="text-end text-prestasi num">{{ number_format($item->achievement_points ?? 0) }}</td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('students.show', $item) }}" class="btn-icon" aria-label="Lihat detail {{ $item->name }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    @can('manage-master-data')
                                        <a href="{{ route('students.edit', $item) }}" class="btn-icon" aria-label="Ubah data {{ $item->name }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </a>
                                        <form method="POST" action="{{ route('students.destroy', $item) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon danger" data-confirm="Siswa akan dinonaktifkan dan tidak muncul di daftar aktif." data-confirm-title="Nonaktifkan {{ $item->name }}?"
                                                aria-label="Nonaktifkan {{ $item->name }}">
                                                <i class="bi bi-person-dash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="8" class="cell-empty">
                                    <x-empty-state icon="people" title="Belum ada siswa" text="Tambahkan siswa satu per satu atau import dari file Excel.">
                                        @can('manage-master-data')
                                            <a href="{{ route('students.create') }}" class="btn btn-primary">Tambah siswa</a>
                                        @endcan
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $students->links() }}
        </div>
    </div>
@endsection
