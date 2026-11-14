@extends('layouts.app')

@section('content')
<div class="container">

    {{-- HEADER --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Peminjaman</h5>
            <a href="/loans/create" class="btn btn-primary btn-sm">+ Pinjam Buku</a>
        </div>
    </div>

    {{-- EXPORT FILTER --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <p class="fw-semibold mb-2">Export Peminjaman</p>

            <form action="{{ route('loans.export') }}"
                  method="GET"
                  class="row g-2 align-items-end">

                <div class="col-auto">
                    <label class="form-label mb-1 small">Dari Tanggal</label>
                    <input type="date"
                           name="from"
                           class="form-control form-control-sm"
                           value="{{ request('from') }}"
                           required>
                </div>

                <div class="col-auto">
                    <label class="form-label mb-1 small">Sampai Tanggal</label>
                    <input type="date"
                           name="to"
                           class="form-control form-control-sm"
                           value="{{ request('to') }}"
                           required>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-sm">
                        ⬇ Export Excel
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Buku</th>
                            <th>Qty</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Pengembalian</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($loans as $loan)
                        <tr>

                            <td class="fw-semibold">
                                {{ $loan->nama_peminjam }}
                            </td>

                            <td>
                                {{ $loan->kelas }}
                            </td>

                            <td>
                                {{ $loan->book->judul ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $loan->qty }}
                                </span>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d-m-Y') }}
                            </td>

                            <td>
                                @if($loan->batas_pengembalian)
                                    {{ \Carbon\Carbon::parse($loan->batas_pengembalian)->format('d-m-Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($loan->tanggal_kembali)
                                    {{ \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d-m-Y') }}
                                @else
                                    <span class="badge bg-secondary">
                                        Belum kembali
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($loan->is_returned)
                                    <span class="badge bg-success">
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        Dipinjam
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($loan->is_returned)

                                    @if($loan->denda > 0)

                                        <span class="text-danger fw-bold">
                                            Rp {{ number_format($loan->denda, 0, ',', '.') }}
                                        </span>

                                        <br>

                                        <small class="text-muted">
                                            Terlambat {{ $loan->denda_hari }} hari
                                        </small>

                                    @else

                                        <span class="text-success">
                                            Tidak ada denda
                                        </span>

                                    @endif

                                @else

                                    <span class="text-muted">-</span>

                                @endif
                            </td>

                            <td class="text-center">

                                @if(!$loan->is_returned)

                                    <form action="/loans/{{ $loan->id }}/return"
                                          method="POST"
                                          class="d-inline return-form">

                                        @csrf

                                        <button type="button"
                                                class="btn btn-success btn-sm btn-return">
                                            Konfirmasi
                                        </button>

                                    </form>

                                @else

                                    <span class="text-muted">
                                        Selesai
                                    </span>

                                @endif

                            </td>

                        </tr>
                        @empty

                        <tr>
                            <td colspan="10"
                                class="text-center text-muted py-4">
                                Tidak ada data peminjaman
                            </td>
                        </tr>

                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer bg-white border-0 pt-3">
            {{ $loans->links('pagination::bootstrap-5') }}
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
    text: @json(session('error')),
    confirmButtonColor: '#dc3545'
});
</script>
@endif

<script>
document.querySelectorAll('.btn-return').forEach(button => {

    button.addEventListener('click', function () {

        const form = this.closest('form');

        Swal.fire({
            title: 'Konfirmasi Pengembalian?',
            text: 'Pastikan buku sudah benar-benar dikembalikan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kembalikan',
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