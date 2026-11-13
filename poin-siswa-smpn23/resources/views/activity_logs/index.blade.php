@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <x-page-head title="Log aktivitas" subtitle="Jejak audit seluruh aktivitas penting, terbaru lebih dulu.">
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2 d-md-none">
                <span class="muted">{{ $logs->total() }} catatan</span>
                <button class="btn btn-outline-secondary btn-filter" type="button" data-bs-toggle="collapse"
                    data-bs-target="#logFilter" aria-expanded="false" aria-controls="logFilter">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filter
                </button>
            </div>

            <form method="GET" class="filter-bar collapse d-md-block" id="logFilter">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="visually-hidden" for="f-search">Cari aksi atau deskripsi</label>
                        <input type="search" id="f-search" name="search" class="form-control"
                            placeholder="Cari aksi / deskripsi" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-user">Pengguna</label>
                        <select name="user_id" id="f-user" class="form-select">
                            <option value="">Semua pengguna</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="visually-hidden" for="f-date">Tanggal</label>
                        <input type="date" id="f-date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
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
                            <th>Pengguna</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="cell-main">{{ $log->user?->name ?? 'Sistem' }}</td>
                                <td class="num" data-label="Waktu">{{ $log->created_at?->format('d M Y H:i:s') }}</td>
                                <td data-label="Aksi">
                                    <span class="tag tag-neutral">{{ $log->action }}</span>
                                </td>
                                <td data-label="Deskripsi">
                                    <span class="list-row-sub">{{ $log->description ?? '-' }}</span>
                                </td>
                                <td class="num" data-label="IP">
                                    <span class="muted">{{ $log->ip_address ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="5" class="cell-empty">
                                    <x-empty-state icon="journal-text" title="Belum ada catatan"
                                        text="Setiap tindakan penting akan tercatat di sini secara otomatis." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        </div>
    </div>
@endsection
