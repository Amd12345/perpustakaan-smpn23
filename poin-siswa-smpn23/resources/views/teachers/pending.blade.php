@extends('layouts.app')

@section('title', 'Persetujuan Guru')

@section('content')
    <x-page-head title="Menunggu persetujuan" subtitle="Guru yang mendaftar mandiri dan belum diaktifkan admin."
        :back="route('teachers.index')" back-label="Data guru" />

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Tanggal daftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $teacher->name }}</td>
                                <td data-label="Email">{{ $teacher->email ?? '-' }}</td>
                                <td data-label="Telepon" class="num">{{ $teacher->phone ?? '-' }}</td>
                                <td data-label="Tanggal daftar">{{ $teacher->created_at?->format('d M Y H:i') }}</td>
                                <td class="cell-actions" data-label="Aksi">
                                    <form method="POST" action="{{ route('teachers.approve', $teacher) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary"
                                            data-confirm="Setujui dan aktifkan akun {{ $teacher->name }}?">
                                            <i class="bi bi-check-lg" aria-hidden="true"></i> Setujui
                                        </button>
                                    </form>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn-icon"
                                        aria-label="Ubah data {{ $teacher->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="hourglass-split" title="Tidak ada pendaftar menunggu"
                                        text="Pendaftar guru baru dari halaman masuk akan muncul di sini untuk disetujui." />
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
