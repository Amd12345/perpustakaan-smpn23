@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')
    <x-page-head title="Tahun ajaran" subtitle="Satu tahun ajaran aktif menjadi acuan pencatatan poin.">
        <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah tahun ajaran
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($years as $year)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $year->name }}</td>
                                <td data-label="Mulai" class="num">{{ $year->start_year }}</td>
                                <td data-label="Selesai" class="num">{{ $year->end_year }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $year->is_active ? 'tag-success' : 'tag-neutral' }}">
                                        {{ $year->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    @unless($year->is_active)
                                        <form method="POST" action="{{ route('academic-years.set-active', $year) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-icon"
                                                data-confirm="Jadikan {{ $year->name }} sebagai tahun ajaran aktif?"
                                                data-confirm-title="Aktifkan {{ $year->name }}?"
                                                aria-label="Aktifkan tahun ajaran {{ $year->name }}">
                                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endunless
                                    <a href="{{ route('academic-years.edit', $year) }}" class="btn-icon"
                                        aria-label="Ubah tahun ajaran {{ $year->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('academic-years.destroy', $year) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm="Hapus tahun ajaran {{ $year->name }}?"
                                            data-confirm-title="Hapus {{ $year->name }}?"
                                            aria-label="Hapus tahun ajaran {{ $year->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="calendar3" title="Belum ada tahun ajaran"
                                        text="Tambahkan tahun ajaran agar poin tercatat per periode.">
                                        <a href="{{ route('academic-years.create') }}" class="btn btn-primary">Tambah tahun ajaran</a>
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
