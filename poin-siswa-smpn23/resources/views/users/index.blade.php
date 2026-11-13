@extends('layouts.app')

@section('title', 'Manajemen Akun')

@section('content')
    @php
        $labels = ['admin' => 'Admin', 'kepala_sekolah' => 'Kepala Sekolah', 'guru' => 'Guru'];
        $roleLabel = $labels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    @endphp

    <x-page-head :title="'Akun '.$roleLabel" :subtitle="$users->total().' akun terdaftar'">
        <a href="{{ route('users.create', $role) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah akun
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Terakhir login</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $user->name }}</td>
                                <td data-label="Email">{{ $user->email }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $user->is_active ? 'tag-success' : 'tag-neutral' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td data-label="Terakhir login">
                                    {{ $user->last_login_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('users.edit', [$role, $user]) }}" class="btn-icon"
                                        aria-label="Ubah akun {{ $user->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('users.toggle-active', [$role, $user]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-icon"
                                            data-confirm-title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}?"
                                            data-confirm="{{ $user->is_active ? 'Akun tidak bisa dipakai masuk sampai diaktifkan kembali.' : 'Akun bisa dipakai masuk kembali.' }}"
                                            aria-label="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}">
                                            <i class="bi bi-{{ $user->is_active ? 'person-dash' : 'person-check' }}" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('users.destroy', [$role, $user]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm-title="Hapus akun {{ $user->name }}?"
                                            data-confirm="Akun dihapus permanen dan tidak bisa dipulihkan."
                                            aria-label="Hapus akun {{ $user->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="person-badge" :title="'Belum ada akun '.$roleLabel"
                                        text="Tambahkan akun pertama supaya petugas bisa masuk ke aplikasi.">
                                        <a href="{{ route('users.create', $role) }}" class="btn btn-primary">Tambah akun</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
@endsection
