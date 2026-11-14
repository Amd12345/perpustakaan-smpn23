@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-4">Form Peminjaman Buku</h5>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ url('/loans') }}" method="POST" id="loan-form">
                @csrf

                {{-- Buku --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Buku</label>
                    <select name="book_id"
                            id="book_id"
                            class="form-select @error('book_id') is-invalid @enderror">
                        <option value="">-- Pilih Buku --</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}"
                                    data-stok="{{ $book->stok }}"
                                    {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->judul }} (Stok: {{ $book->stok }})
                            </option>
                        @endforeach
                    </select>
                    @error('book_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="stok-info" class="form-text mt-1"></div>
                </div>

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Peminjam</label>
                    <input type="text"
                           name="nama_peminjam"
                           value="{{ old('nama_peminjam') }}"
                           class="form-control @error('nama_peminjam') is-invalid @enderror"
                           placeholder="Masukkan nama peminjam">
                    @error('nama_peminjam')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kelas --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas</label>
                    <select name="kelas"
                            class="form-select @error('kelas') is-invalid @enderror">
                        <option value="">-- Pilih Kelas --</option>

                        <optgroup label="Kelas 7">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="7.{{ $i }}" {{ old('kelas') == "7.$i" ? 'selected' : '' }}>
                                    7.{{ $i }}
                                </option>
                            @endfor
                        </optgroup>

                        <optgroup label="Kelas 8">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="8.{{ $i }}" {{ old('kelas') == "8.$i" ? 'selected' : '' }}>
                                    8.{{ $i }}
                                </option>
                            @endfor
                        </optgroup>

                        <optgroup label="Kelas 9">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="9.{{ $i }}" {{ old('kelas') == "9.$i" ? 'selected' : '' }}>
                                    9.{{ $i }}
                                </option>
                            @endfor
                        </optgroup>
                    </select>
                    @error('kelas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tanggal Pinjam --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pinjam</label>
                    <input type="date"
                           name="tanggal_pinjam"
                           value="{{ old('tanggal_pinjam') }}"
                           class="form-control @error('tanggal_pinjam') is-invalid @enderror">
                    @error('tanggal_pinjam')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jumlah Buku --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah Buku</label>
                    <input type="number"
                           name="qty"
                           id="qty"
                           min="1"
                           value="{{ old('qty', 1) }}"
                           class="form-control @error('qty') is-invalid @enderror">
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Button --}}
                <div class="d-flex gap-2">
                    <button type="submit" id="btn-submit" class="btn btn-primary">Simpan</button>
                    <a href="/loans" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Select2 CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let currentStok = 0;

    function updateStokInfo(stok, hasValue) {
        const infoEl = document.getElementById('stok-info');
        const qtyEl  = document.getElementById('qty');
        const btnEl  = document.getElementById('btn-submit');

        if (hasValue && stok >= 0) {
            currentStok = stok;
            qtyEl.max   = stok;

            if (stok === 0) {
                infoEl.textContent = '⚠️ Stok habis, buku tidak bisa dipinjam.';
                infoEl.className   = 'form-text text-danger mt-1';
                btnEl.disabled     = true;
            } else {
                infoEl.textContent = `Stok tersedia: ${stok} buku`;
                infoEl.className   = 'form-text text-success mt-1';
                btnEl.disabled     = false;
            }
        } else {
            currentStok            = 0;
            infoEl.textContent     = '';
            infoEl.className       = 'form-text mt-1';
            btnEl.disabled         = false;
            qtyEl.removeAttribute('max');
        }
    }

    $(document).ready(function () {
        $('#book_id').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih atau cari buku --',
            allowClear: true,
        }).on('change', function () {
            const selected = $(this).find(':selected');
            const stok     = parseInt(selected.data('stok') ?? -1);
            updateStokInfo(stok, !!$(this).val());
        });

        // Jalankan saat load kalau ada old('book_id')
        if ($('#book_id').val()) {
            const stok = parseInt($('#book_id').find(':selected').data('stok') ?? -1);
            updateStokInfo(stok, true);
        }
    });

    // Pop up saat qty diubah melebihi stok
    document.getElementById('qty').addEventListener('input', function () {
        if (currentStok === 0) return;

        const qty = parseInt(this.value);
        if (qty > currentStok) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok Tidak Cukup!',
                text: `Kamu memasukkan ${qty} buku, tapi stok yang tersedia hanya ${currentStok}.`,
                confirmButtonText: 'Oke, saya mengerti',
                confirmButtonColor: '#0d6efd',
            });
            this.value = currentStok;
        }
    });

    // Double-check saat submit
    document.getElementById('loan-form').addEventListener('submit', function (e) {
        const qty   = parseInt(document.getElementById('qty').value);
        const judul = $('#book_id').find(':selected').text();

        if (currentStok > 0 && qty > currentStok) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tidak Bisa Meminjam!',
                html: `Jumlah yang kamu minta (<b>${qty}</b>) melebihi stok yang tersedia (<b>${currentStok}</b>) untuk <b>${judul}</b>.`,
                confirmButtonText: 'Kembali & Perbaiki',
                confirmButtonColor: '#dc3545',
            });
        }
    });
</script>
@endsection