@extends('layouts.app')

@section('title', 'Kategori Pelanggaran')

@section('content')
    <x-page-head title="Kategori pelanggaran"
        :subtitle="'Total '.$categories->total().' kategori. Hanya kategori aktif yang muncul saat mencatat pelanggaran.'">
        <a href="{{ route('violation-categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah kategori
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $category->name }}</td>
                                <td data-label="Deskripsi">{{ $category->description ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $category->is_active ? 'tag-success' : 'tag-neutral' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('violation-categories.edit', $category) }}" class="btn-icon" aria-label="Ubah kategori {{ $category->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('violation-categories.destroy', $category) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon danger"
                                            data-confirm="Kategori yang sudah dipakai tidak dapat dihapus."
                                            data-confirm-title="Hapus kategori {{ $category->name }}?"
                                            aria-label="Hapus kategori {{ $category->name }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="4" class="cell-empty">
                                    <x-empty-state icon="list-check" title="Belum ada kategori" text="Tambahkan kategori agar setiap pelanggaran tercatat dengan poin yang tepat.">
                                        <a href="{{ route('violation-categories.create') }}" class="btn btn-primary">Tambah kategori</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
@endsection
