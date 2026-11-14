@extends('layouts.app')

@section('content')

<div class="container">

    <!-- HEADER -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Buku</h5>

            <div>
                <a href="{{ route('books.export') }}" class="btn btn-success btn-sm me-1">
                    Export Excel
                </a>

                <button class="btn btn-primary btn-sm me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#importModal">
                    Import Excel
                </button>

                <a href="/books/create" class="btn btn-success btn-sm">
                    + Tambah Buku
                </a>
            </div>
        </div>
    </div>

    <!-- SEARCH -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <form method="GET">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari buku..."
                           value="{{ request('search') }}">

                    <button class="btn btn-primary">
                        Cari
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Penerbit</th>
                            <th>Tahun Terbit</th>
                            <th>Stok</th>
                            <th class="text-center" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td class="fw-semibold">{{ $book->judul }}</td>
                            <td>{{ $book->penerbit }}</td>
                            <td>{{ $book->tahun_terbit }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $book->stok }}
                                </span>
                            </td>

                            <td class="text-center">

                                <a href="/books/{{ $book->id }}/edit"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="/books/{{ $book->id }}"
                                      method="POST"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-delete">
                                        Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-4">
                                Tidak ada data buku
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white border-0 pt-3">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Data Buku</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('books.import') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">

                    <label class="form-label">
                        Pilih File Excel
                    </label>

                    <input type="file"
                           name="file"
                           class="form-control"
                           required>

                    <small class="text-muted">
                        Format: .xlsx atau .csv — Header harus:
                        judul, penerbit, tahun_terbit, stok
                    </small>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Import
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: @json(session('success')),
    confirmButtonColor: '#198754'
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Terjadi Kesalahan',
    html: @json(session('error')),
    confirmButtonColor: '#dc3545'
});
</script>
@endif

<script>
document.querySelectorAll('.btn-delete').forEach(button => {

    button.addEventListener('click', function () {

        const form = this.closest('form');

        Swal.fire({
            title: 'Hapus Buku?',
            text: 'Data buku yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {
                form.submit();
            }

        });

    });

});
</script>

@endsection