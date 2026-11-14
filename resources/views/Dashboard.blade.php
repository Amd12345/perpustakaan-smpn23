@extends('layouts.app')

@section('content')

<div class="container">

    <!-- STATISTIC -->
    <div class="row g-3 mb-4">

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Buku Dipinjam</h6>
                    <h2 class="fw-bold text-primary">{{ $dipinjam }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Buku Tersedia</h6>
                    <h2 class="fw-bold text-success">{{ $tersedia }}</h2>
                </div>
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
                           placeholder="Cari buku...">

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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
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

@endsection