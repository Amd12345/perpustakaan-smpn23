@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
    <x-page-head title="Data guru" :subtitle="$teachers->total().' guru terdaftar'">
        <a href="{{ route('teachers.pending') }}" class="btn btn-outline-secondary">
            <i class="bi bi-hourglass-split" aria-hidden="true"></i> Menunggu persetujuan
        </a>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah guru
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted">{{ $teachers->total() }} guru</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#teacherFilter" aria-expanded="false" aria-controls="teacherFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="teacherFilter">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="visually-hidden" for="f-search">Cari nama, NIP, atau email</label>
                        <input type="search" id="f-search" name="search" class="form-control"
                            placeholder="Cari nama / NIP / email" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-status">Status akun</label>
                        <select name="status" id="f-status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="aktif" @selected(request('status') == 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(request('status') == 'nonaktif')>Nonaktif</option>
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
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Akun</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $teacher->name }}</td>
                                <td data-label="NIP" class="num">{{ $teacher->nip ?? '-' }}</td>
                                <td data-label="Email">{{ $teacher->email ?? '-' }}</td>
                                <td data-label="Telepon" class="num">{{ $teacher->phone ?? '-' }}</td>
                                <td data-label="Akun">
                                    @if($teacher->user)
                                        <span class="tag tag-brand">{{ $teacher->user->email }}</span>
                                    @else
                                        <span class="tag tag-neutral">Tanpa akun</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    <span class="tag {{ $teacher->is_active ? 'tag-success' : 'tag-neutral' }}">
                                        {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn-icon"
                                        aria-label="Ubah data {{ $teacher->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('teachers.toggle-active', $teacher) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-icon"
                                            data-confirm="{{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan' }} guru {{ $teacher->name }}?"
                                            aria-label="{{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $teacher->name }}">
                                            <i class="bi bi-{{ $teacher->is_active ? 'person-dash' : 'person-check' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm="Hapus data guru {{ $teacher->name }}?"
                                            aria-label="Hapus data {{ $teacher->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="7" class="cell-empty">
                                    <x-empty-state icon="person-badge" title="Belum ada data guru"
                                        text="Tambahkan guru satu per satu agar akun dan penugasan bisa dikelola.">
                                        <a href="{{ route('teachers.create') }}" class="btn btn-primary">Tambah guru</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $teachers->links() }}
        </div>
    </div>
@endsection
