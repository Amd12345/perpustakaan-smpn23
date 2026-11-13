@extends('layouts.app')

@section('title', 'Konversi Poin')

@section('content')
    <x-page-head title="Konversi poin" subtitle="Prestasi dikonversi menjadi pengurangan poin pelanggaran">
        <a href="{{ route('conversions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Buat konversi
        </a>
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Tanggal</th>
                            <th>Prestasi</th>
                            <th class="text-end">Poin dikonversi</th>
                            <th class="text-end">Pengurangan</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversions as $item)
                            <tr>
                                <td class="cell-main" data-label="Siswa">
                                    {{ $item->student?->name ?? '-' }}
                                    <div class="list-row-sub">{{ $item->student?->class?->name ?? '-' }}</div>
                                </td>
                                <td class="num" data-label="Tanggal">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td data-label="Prestasi">{{ $item->achievement?->category?->name ?? '-' }}</td>
                                <td data-label="Poin dikonversi" class="text-end text-prestasi num">{{ $item->conversion_points }}</td>
                                <td data-label="Pengurangan" class="text-end text-pelanggaran num">-{{ $item->reduction_points }}</td>
                                <td data-label="Petugas">{{ $item->teacher?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="6" class="cell-empty">
                                    <x-empty-state icon="arrow-left-right" title="Belum ada konversi"
                                        text="Konversi dibuat saat poin prestasi siswa ditukar menjadi pengurangan poin pelanggaran.">
                                        <a href="{{ route('conversions.create') }}" class="btn btn-primary">Buat konversi</a>
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $conversions->links() }}
        </div>
    </div>
@endsection
