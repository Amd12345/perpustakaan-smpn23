@extends('layouts.app')

@section('title', 'Data kelas')

@section('content')
    <x-page-head title="Data kelas" :subtitle="'Tahun ajaran '.($year?->name ?? '-')">
        <a href="{{ route('classes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah kelas
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted" style="font-size:13px">{{ $classes->count() }} kelas</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#classFilter" aria-expanded="false" aria-controls="classFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="classFilter">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <label class="visually-hidden" for="f-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="f-year" class="form-select">
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(($year?->id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
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
                            <th>Kelas</th>
                            <th>Tahun ajaran</th>
                            <th>Wali kelas</th>
                            <th class="text-end">Jumlah siswa</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td class="cell-main" data-label="Kelas">{{ $class->name }}</td>
                                <td data-label="Tahun ajaran">{{ $class->academicYear?->name ?? '-' }}</td>
                                <td data-label="Wali kelas">{{ $class->homeroomTeacher?->name ?? '-' }}</td>
                                <td data-label="Jumlah siswa" class="text-end num">{{ $class->students_count }}</td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('classes.edit', $class) }}" class="btn-icon" aria-label="Ubah kelas {{ $class->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('classes.destroy', $class) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm="Kelas akan dihapus bila tidak lagi terhubung dengan data lain."
                                            data-confirm-title="Hapus kelas {{ $class->name }}?"
                                            aria-label="Hapus kelas {{ $class->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="grid-3x3-gap" title="Belum ada kelas" text="Tambahkan kelas pada tahun ajaran ini agar siswa bisa dikelompokkan.">
                                        <a href="{{ route('classes.create') }}" class="btn btn-primary">Tambah kelas</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
