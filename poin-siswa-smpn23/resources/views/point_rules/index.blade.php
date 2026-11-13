@extends('layouts.app')

@section('title', 'Aturan Poin')

@section('content')
    <x-page-head title="Aturan poin" :subtitle="'Tahun ajaran '.($year?->name ?? 'belum diatur')" />

    <form method="GET" action="{{ route('point-rules.index') }}" class="filter-bar">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" for="f-year">Tahun ajaran</label>
                <select name="academic_year_id" id="f-year" class="form-select">
                    @foreach($years as $item)
                        <option value="{{ $item->id }}" @selected(($year?->id) == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search" aria-hidden="true"></i> Terapkan
                </button>
            </div>
        </div>
    </form>

    <div class="grid-2">
        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title">
                    <i class="bi bi-exclamation-octagon" aria-hidden="true"></i> Pelanggaran
                </h2>

                <div class="table-responsive">
                    <table class="table table-hover table-stack align-middle">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Poin</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($violationCategories as $category)
                                @php $rule = $category->pointRules->first(); @endphp
                                <tr>
                                    <td class="cell-main" data-label="Kategori">
                                        {{ $category->name }}
                                        @unless($rule)
                                            <span class="list-row-sub d-block">Belum diatur</span>
                                        @endunless
                                    </td>
                                    <td data-label="Poin">
                                        <form method="POST" action="{{ route('point-rules.store') }}" class="d-flex gap-1 justify-content-end">
                                            @csrf
                                            <input type="hidden" name="ruleable_type" value="{{ \App\Models\ViolationCategory::class }}">
                                            <input type="hidden" name="ruleable_id" value="{{ $category->id }}">
                                            <input type="hidden" name="academic_year_id" value="{{ $year?->id }}">
                                            <label class="visually-hidden" for="violation-point-{{ $category->id }}">Poin pelanggaran {{ $category->name }}</label>
                                            <input type="number" name="point_value" id="violation-point-{{ $category->id }}"
                                                class="form-control form-control-sm text-end num" style="max-width: 90px"
                                                min="0" value="{{ $rule?->point_value ?? 0 }}" required>
                                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                                aria-label="Simpan poin {{ $category->name }}">
                                                <i class="bi bi-save" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="cell-actions" data-label="Aksi">
                                        @if($rule)
                                            <form method="POST" action="{{ route('point-rules.destroy', $rule) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon danger"
                                                    data-confirm="Aturan poin kategori ini akan dihapus untuk tahun ajaran {{ $year?->name ?? 'yang dipilih' }}."
                                                    data-confirm-title="Hapus aturan poin {{ $category->name }}?"
                                                    aria-label="Hapus aturan poin {{ $category->name }}">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="row-empty">
                                    <td colspan="3" class="cell-empty">
                                        <x-empty-state icon="exclamation-octagon" title="Belum ada kategori pelanggaran"
                                            text="Tambahkan kategori dulu, lalu isi bobot poinnya di sini.">
                                            <a href="{{ route('violation-categories.index') }}" class="btn btn-primary">Tambah kategori</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title">
                    <i class="bi bi-trophy" aria-hidden="true"></i> Prestasi
                </h2>

                <div class="table-responsive">
                    <table class="table table-hover table-stack align-middle">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Poin</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($achievementCategories as $category)
                                @php $rule = $category->pointRules->first(); @endphp
                                <tr>
                                    <td class="cell-main" data-label="Kategori">
                                        {{ $category->name }}
                                        @if($category->level)
                                            <span class="list-row-sub d-block">{{ ucfirst($category->level) }}</span>
                                        @endif
                                        @unless($rule)
                                            <span class="list-row-sub d-block">Belum diatur</span>
                                        @endunless
                                    </td>
                                    <td data-label="Poin">
                                        <form method="POST" action="{{ route('point-rules.store') }}" class="d-flex gap-1 justify-content-end">
                                            @csrf
                                            <input type="hidden" name="ruleable_type" value="{{ \App\Models\AchievementCategory::class }}">
                                            <input type="hidden" name="ruleable_id" value="{{ $category->id }}">
                                            <input type="hidden" name="academic_year_id" value="{{ $year?->id }}">
                                            <label class="visually-hidden" for="achievement-point-{{ $category->id }}">Poin prestasi {{ $category->name }}</label>
                                            <input type="number" name="point_value" id="achievement-point-{{ $category->id }}"
                                                class="form-control form-control-sm text-end num" style="max-width: 90px"
                                                min="0" value="{{ $rule?->point_value ?? 0 }}" required>
                                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                                aria-label="Simpan poin {{ $category->name }}">
                                                <i class="bi bi-save" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="cell-actions" data-label="Aksi">
                                        @if($rule)
                                            <form method="POST" action="{{ route('point-rules.destroy', $rule) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon danger"
                                                    data-confirm="Aturan poin kategori ini akan dihapus untuk tahun ajaran {{ $year?->name ?? 'yang dipilih' }}."
                                                    data-confirm-title="Hapus aturan poin {{ $category->name }}?"
                                                    aria-label="Hapus aturan poin {{ $category->name }}">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="row-empty">
                                    <td colspan="3" class="cell-empty">
                                        <x-empty-state icon="trophy" title="Belum ada kategori prestasi"
                                            text="Tambahkan kategori dulu, lalu isi bobot poinnya di sini.">
                                            <a href="{{ route('achievement-categories.index') }}" class="btn btn-primary">Tambah kategori</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle" aria-hidden="true"></i>
        <div>Tombol simpan membuat aturan baru bila belum ada, atau memperbarui bobot poin yang sudah ada pada tahun ajaran terpilih.</div>
    </div>
@endsection
