# Panduan UI poin-siswa-smpn23

Arah visual: netral terang abu-biru, terasa seperti aplikasi dinas. Terang saja (tanpa mode gelap).
Prioritas: nyaman dipakai di HP oleh guru di sekolah.

Semua halaman wajib memakai layout dan kelas di bawah ini. Jangan menulis CSS baru di view
(kecuali posisi kecil tanpa warna/ukuran baru), jangan memuat CSS/JS dari CDN.

## Kerangka halaman

```blade
@extends('layouts.app')

@section('title', 'Judul singkat')   {{-- jadi <title>; h1 ada di page-head --}}

@section('content')
    <x-page-head title="Judul halaman" :subtitle="'Keterangan pendukung'"
        :back="route('nama.index')" back-label="Kembali">
        {{-- slot aksi: tombol kanan atas. Boleh kosong --}}
        <a href="..." class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah data</a>
    </x-page-head>
    ...
@endsection
```

Aturan:
- Satu `<h1>` per halaman, berasal dari `<x-page-head>`. Judul section memakai `<h2 class="card-section-title">`.
- Staf mengenal istilah "Ubah" bukan "Edit", "Hapus" bukan "Delete", "Simpan" bukan "Submit".
- Jangan pakai tanda pisah panjang (em dash) di teks apa pun.
- Semua ikon dekoratif diberi `aria-hidden="true"`. Tombol ikon saja wajib punya `aria-label`.

## Komponen yang tersedia

| Kebutuhan | Markah |
|---|---|
| Kartu | `<div class="card"><div class="card-body">...</div></div>` |
| Judul dalam kartu | `<h2 class="card-section-title"><i class="bi bi-x" aria-hidden="true"></i> Teks</h2>` |
| Angka ringkas | `.stat-grid` > `.stat` > `.stat-top` (`.stat-label`, `.stat-icon tint-brand/tint-danger/tint-success/tint-accent/tint-neutral`) + `.stat-value` |
| Saldo besar (fokus layar) | `.focus-panel` > `.balance balance-pelanggaran` / `.balance balance-prestasi` (`.balance-label`, `.balance-value`, `.balance-meta`) |
| Tombol | `btn btn-primary`, `btn btn-outline-secondary`, `btn btn-danger`; ukuran `btn-sm`; ikon saja: `btn-icon` (plus `danger`) |
| Aksi tabel | `btn-icon` dengan `aria-label` |
| Label status | `<span class="tag tag-success|tag-danger|tag-accent|tag-neutral|tag-brand">` |
| Grid 2/3 kolom | `<div class="grid-2">` atau `<div class="grid-3">` (otomatis 1 kolom di HP) |
| Daftar baris | `.list-row` > `.list-row-main` (`.list-row-title`, `.list-row-sub`) + `.list-row-end` |
| Peringkat | `.rank-row` > `.rank-no` + `.list-row-main` + `.list-row-end` |
| Detail kunci-nilai | `<dl class="detail-list">` + `.detail-item` berisi `<dt>` dan `<dd>` |
| Kosong | `<x-empty-state icon="inbox" title="Belum ada data" text="Penjelasan singkat.">opsional tombol</x-empty-state>` |
| Form | `.row g-3`, label `<label class="form-label" for="...">`, input `.form-control`, select `.form-select`, error `.invalid-feedback` |
| Aksi form | `.form-actions` dengan urutan DOM: Batal dulu, tombol simpan terakhir |
| Filter | `.filter-bar` + `.collapse d-md-block` (tombol "Filter" hanya tampil di HP dengan `d-md-none`) |
| Peringatan | `.alert alert-info / alert-warning / alert-danger` dengan ikon di kiri |
| Grafik | `.chart-box` (`.is-bar` atau `.is-doughnut`) membungkus `<canvas>` |

Angka poin selalu diberi `.num` (angka tabular). Poin pelanggaran memakai `.text-pelanggaran`,
poin prestasi `.text-prestasi`.

## Pola tabel yang nyaman di HP

Tabel wajib `table table-hover table-stack align-middle`, sel pertama berisi nama entitas diberi
`class="cell-main"`, setiap `<td>` lain diberi `data-label="Nama Kolom"`, sel aksi
`class="cell-actions" data-label="Aksi"`. Baris kosong:

```blade
<tr class="row-empty">
    <td colspan="7" class="cell-empty">
        <x-empty-state icon="inbox" title="Belum ada data" text="..." />
    </td>
</tr>
```

Kolom `text-end` tetap dipakai untuk angka. Di HP tabel berubah menjadi kartu berlabel, jadi
`data-label` wajib ada dan ringkas (contoh `Poin`, bukan `Jumlah Poin Pelanggaran`).

## Form

- Satu kolom di HP: gunakan `col-12` atau `col-6` untuk dua field pendek berdampingan (tanggal + poin).
- Input file: sertakan `.form-text` penjelasan jenis berkas.
- Pesan error per field: `@error('x')<div class="invalid-feedback">{{ $message }}</div>@enderror` dan tambahkan `is-invalid`.
- Ringkasan error global sudah ditangani `partials/flash` di layout, jangan ulangi daftar error di atas form.

## Bahasa dan nada

- Sebut hasil aksi: "Simpan pelanggaran", "Terapkan filter", "Buat persyaratan".
- Kalimat aktif, sapaan netral (jangan "kamu"), istilah sekolah yang lazim.
- Kosong bukan kegagalan: sebutkan langkah berikutnya.
