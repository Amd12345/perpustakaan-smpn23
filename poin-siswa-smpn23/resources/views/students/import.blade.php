@extends('layouts.app')

@section('title', 'Import data siswa')

@section('content')
    <x-page-head title="Import data siswa" subtitle="Unggah file Excel berisi data siswa untuk satu tahun ajaran."
        :back="route('students.index')" back-label="Data siswa" />

    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <div>
                    <strong>Kolom yang dibutuhkan:</strong>
                    <code>nisn, nama, jenis_kelamin, kelas, tahun_ajaran, tanggal_lahir, alamat, nama_orang_tua, telepon_orang_tua, hubungan</code>.
                    Kelas harus sudah terdaftar pada tahun ajaran yang dituju.
                </div>
            </div>

            <form method="POST" action="{{ route('students.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="file">File Excel</label>
                        <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Berkas .xlsx, .xls, atau .csv. Baris pertama dipakai sebagai judul kolom.</div>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload" aria-hidden="true"></i> Proses import
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
