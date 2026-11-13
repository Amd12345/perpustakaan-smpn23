@extends('layouts.app')

@section('title', 'Data Orang Tua')

@section('content')
    <x-page-head title="Data orang tua" :subtitle="$parents->total().' orang tua dan wali terdaftar'">
        <a href="{{ route('parents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah orang tua
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted">{{ $parents->total() }} orang tua</span>
                <button class="btn btn-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#parentFilter" aria-expanded="false" aria-controls="parentFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="parentFilter">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="visually-hidden" for="f-search">Cari nama atau nomor telepon</label>
                        <input type="search" id="f-search" name="search" class="form-control"
                            placeholder="Cari nama / nomor telepon" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search" aria-hidden="true"></i> Cari
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th class="text-end">Jumlah anak</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $parent)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $parent->name }}</td>
                                <td data-label="Telepon" class="num">{{ $parent->phone }}</td>
                                <td data-label="Alamat">{{ $parent->address ?? '-' }}</td>
                                <td data-label="Jumlah anak" class="text-end num">{{ $parent->students_count }}</td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('parents.edit', $parent) }}" class="btn-icon"
                                        aria-label="Ubah data {{ $parent->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('parents.destroy', $parent) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm="Hapus data orang tua {{ $parent->name }}?"
                                            aria-label="Hapus data {{ $parent->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="people" title="Belum ada data orang tua"
                                        text="Tambahkan orang tua atau wali agar kontak dan siswa terhubung.">
                                        <a href="{{ route('parents.create') }}" class="btn btn-primary">Tambah orang tua</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $parents->links() }}
        </div>
    </div>
@endsection
