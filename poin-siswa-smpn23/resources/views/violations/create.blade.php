@extends('layouts.app')

@section('title', 'Catat Pelanggaran')

@section('content')
    <x-page-head title="Catat pelanggaran" subtitle="Poin mengikuti kategori yang dipilih."
        :back="route('violations.index')" back-label="Data pelanggaran" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('violations.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="student-filter">Siswa</label>
                        <input type="search" id="student-filter" class="form-control mb-2" placeholder="Ketik nama untuk mempersempit"
                            autocomplete="off" aria-controls="student-select">
                        <select name="student_id" id="student-select" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Pilih siswa</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                    {{ $student->name }} ({{ $student->class?->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="category">Jenis pelanggaran</label>
                        <select name="violation_category_id" id="category" class="form-select @error('violation_category_id') is-invalid @enderror" required>
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('violation_category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('violation_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="incident_date">Tanggal kejadian</label>
                        <input type="date" name="incident_date" id="incident_date" class="form-control @error('incident_date') is-invalid @enderror"
                            value="{{ old('incident_date', now()->toDateString()) }}" required>
                        @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="academic-year">Tahun ajaran</label>
                        <select name="academic_year_id" id="academic-year" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            @foreach(\App\Models\AcademicYear::orderByDesc('start_year')->get() as $item)
                                <option value="{{ $item->id }}" @selected((old('academic_year_id') ?: $year?->id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Poin yang akan diberikan</label>
                        <div class="point-preview">
                            <span class="muted" style="font-size:13px">Otomatis dari kategori</span>
                            <strong id="point-preview" class="text-pelanggaran">-</strong>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="description">Keterangan</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" required
                            placeholder="Apa yang terjadi, di mana, dan siapa yang melihat">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="evidence">Bukti (opsional)</label>
                        <input type="file" name="evidence" id="evidence" class="form-control @error('evidence') is-invalid @enderror"
                            accept="image/*,.pdf,.doc,.docx">
                        <div class="form-text">Foto, PDF, atau dokumen. Berguna saat orang tua menanyakan catatan.</div>
                        @error('evidence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const filtered = {
                {{ $year?->id }}: @json($pointMap)
            };

            const yearSelect = document.getElementById('academic-year');
            const categorySelect = document.getElementById('category');
            const preview = document.getElementById('point-preview');

            function refreshPoints() {
                const map = filtered[yearSelect.value] || {};
                const value = map[categorySelect.value];
                preview.textContent = value !== undefined ? '+' + value : '-';
            }

            categorySelect.addEventListener('change', refreshPoints);
            yearSelect.addEventListener('change', refreshPoints);
            refreshPoints();

            // Pilih siswa cepat: saring opsi lalu pilih otomatis jika tinggal satu.
            const filterInput = document.getElementById('student-filter');
            const studentSelect = document.getElementById('student-select');
            const options = Array.from(studentSelect.options);

            filterInput.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();
                let matches = 0;
                let lastMatch = null;

                options.forEach(function (option) {
                    if (!option.value) return;
                    const hit = option.text.toLowerCase().includes(term);
                    option.hidden = term !== '' && !hit;
                    if (hit && term !== '') {
                        matches += 1;
                        lastMatch = option;
                    }
                });

                if (term === '') {
                    studentSelect.value = '';
                } else if (matches === 1) {
                    studentSelect.value = lastMatch.value;
                }
            });
        })();
    </script>
@endsection
