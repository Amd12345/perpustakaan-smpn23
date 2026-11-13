@extends('layouts.app')

@section('title', 'Kategori Prestasi')

@section('content')
    <x-page-head title="Kategori prestasi"
        :subtitle="'Total '.$categories->total().' kategori. Hanya kategori aktif yang muncul saat mencatat prestasi.'">
        <a href="{{ route('achievement-categories.create') }}" class="btn btn-primary">
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
                            <th>Tingkat</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="cell-main" data-label="Nama">{{ $category->name }}</td>
                                <td data-label="Tingkat">
                                    @if($category->level)
                                        <span class="tag tag-accent">{{ ucfirst($category->level) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Deskripsi">{{ $category->description ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $category->is_active ? 'tag-success' : 'tag-neutral' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('achievement-categories.edit', $category) }}" class="btn-icon" aria-label="Ubah kategori {{ $category->name }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="POST" action="{{ route('achievement-categories.destroy', $category) }}" class="d-inline">
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
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="award" title="Belum ada kategori" text="Tambahkan kategori agar setiap prestasi tercatat dengan poin yang tepat.">
                                        <a href="{{ route('achievement-categories.create') }}" class="btn btn-primary">Tambah kategori</a>
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
