@extends('layouts.app')

@section('title', 'Notifikasi WhatsApp')

@section('content')
    <x-page-head title="Notifikasi WhatsApp" subtitle="Semua pengiriman notifikasi ke orang tua, terbaru lebih dulu.">
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted">{{ $notifications->total() }} notifikasi</span>
                <button class="btn btn-outline-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#notificationFilter" aria-expanded="false" aria-controls="notificationFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="notificationFilter">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-search">Cari siswa atau NISN</label>
                        <input type="search" id="f-search" name="search" class="form-control"
                            placeholder="Cari siswa / NISN" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-status">Status</label>
                        <select name="status" id="f-status" class="form-select">
                            <option value="">Semua status</option>
                            <option value="tertunda" @selected(request('status') == 'tertunda')>Tertunda</option>
                            <option value="terkirim" @selected(request('status') == 'terkirim')>Terkirim</option>
                            <option value="gagal" @selected(request('status') == 'gagal')>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-type">Jenis notifikasi</label>
                        <select name="type" id="f-type" class="form-select">
                            <option value="">Semua jenis</option>
                            @foreach(['pelanggaran_ambang','prestasi','persyaratan','pengurangan','penolakan','konversi'] as $type)
                                <option value="{{ $type }}" @selected(request('type') == $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Waktu</th>
                            <th>Orang tua</th>
                            <th>Jenis</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $item)
                            <tr>
                                <td class="cell-main">{{ $item->student?->name }}</td>
                                <td class="num" data-label="Waktu">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td data-label="Orang tua">
                                    {{ $item->parent?->name }}
                                    <div class="list-row-sub num">{{ $item->recipient_phone }}</div>
                                </td>
                                <td data-label="Jenis">
                                    <span class="tag tag-neutral">{{ str_replace('_', ' ', $item->type) }}</span>
                                </td>
                                <td data-label="Pesan">
                                    <span class="list-row-sub">{{ Str::limit($item->message, 90) }}</span>
                                </td>
                                <td data-label="Status">
                                    <span class="tag {{ $item->status === 'terkirim' ? 'tag-success' : ($item->status === 'gagal' ? 'tag-danger' : 'tag-accent') }}">{{ ucfirst($item->status) }}</span>
                                    @if($item->status === 'gagal' && $item->error)
                                        <div class="list-row-sub text-pelanggaran">{{ Str::limit($item->error, 60) }}</div>
                                    @endif
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    @if($item->status !== 'terkirim')
                                        <form method="POST" action="{{ route('notifications.retry', $item) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-icon" data-confirm="Kirim ulang notifikasi ini?"
                                                aria-label="Kirim ulang notifikasi untuk {{ $item->student?->name ?? 'siswa ini' }}">
                                                <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="list-row-sub">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="7" class="cell-empty">
                                    <x-empty-state icon="chat-dots" title="Belum ada notifikasi"
                                        text="Notifikasi muncul setelah ada pelanggaran, prestasi, atau persyaratan yang dikirim ke orang tua." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $notifications->links() }}
        </div>
    </div>
@endsection
