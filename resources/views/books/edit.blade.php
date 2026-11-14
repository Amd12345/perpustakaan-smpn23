@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h5 class="mb-3">Edit Buku</h5>

            <form action="{{ url('/books/' . $book->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text"
                           name="judul"
                           class="form-control @error('judul') is-invalid @enderror"
                           value="{{ old('judul', $book->judul) }}"
                           required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text"
                           name="penerbit"
                           class="form-control @error('penerbit') is-invalid @enderror"
                           value="{{ old('penerbit', $book->penerbit) }}"
                           required>
                    @error('penerbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number"
                           name="tahun_terbit"
                           class="form-control @error('tahun_terbit') is-invalid @enderror"
                           value="{{ old('tahun_terbit', $book->tahun_terbit) }}"
                           required>
                    @error('tahun_terbit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number"
                           name="stok"
                           class="form-control @error('stok') is-invalid @enderror"
                           value="{{ old('stok', $book->stok) }}"
                           required>
                    @error('stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">
                    Update
                </button>

                <a href="/books" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

@endsection
