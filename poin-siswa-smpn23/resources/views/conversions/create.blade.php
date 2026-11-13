@extends('layouts.app')

@section('title', 'Buat Konversi Poin')

@section('content')
    <x-page-head title="Buat konversi poin" :subtitle="'Tahun ajaran '.($year?->name ?? '-')"
        :back="route('conversions.index')" back-label="Konversi poin" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('conversions.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="student">Siswa</label>
                        <select name="student_id" id="student" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">Pilih siswa</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} ({{ $student->class?->name }})</option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="achievement">Prestasi</label>
                        <select name="achievement_id" id="achievement" class="form-select @error('achievement_id') is-invalid @enderror" required>
                            <option value="">Pilih siswa terlebih dahulu</option>
                        </select>
                        <div class="form-text">Hanya prestasi dengan poin tersisa yang bisa dikonversi.</div>
                        @error('achievement_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label" for="points">Poin prestasi dikonversi</label>
                        <input type="number" name="conversion_points" id="points"
                            class="form-control @error('conversion_points') is-invalid @enderror" min="1"
                            value="{{ old('conversion_points') }}" required>
                        @error('conversion_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label">Rasio konversi</label>
                        <div class="point-preview">
                            <span class="muted" style="font-size:13px">Prestasi : pelanggaran</span>
                            <strong id="ratio-preview">-</strong>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label">Pengurangan poin pelanggaran</label>
                        <div class="point-preview">
                            <span class="muted" style="font-size:13px">Hasil konversi</span>
                            <strong id="reduction-preview" class="text-pelanggaran">-</strong>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('conversions.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" data-confirm="Konversi poin prestasi menjadi pengurangan poin pelanggaran?">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan konversi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const achievements = {!! $achievementOptions !!};
        const ratio = {{ (int) \App\Models\Setting::getValue('point_conversion_ratio', 1) }};
        const studentSelect = document.getElementById('student');
        const achievementSelect = document.getElementById('achievement');
        const pointsInput = document.getElementById('points');

        studentSelect.addEventListener('change', function () {
            const studentId = this.value;
            const items = achievements.filter((item) => String(item.student_id) === String(studentId));
            achievementSelect.innerHTML = '<option value="">Pilih prestasi</option>';
            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.label;
                option.dataset.remaining = item.remaining;
                achievementSelect.appendChild(option);
            });
            refreshPreview();
        });

        achievementSelect.addEventListener('change', refreshPreview);
        pointsInput.addEventListener('input', refreshPreview);

        function refreshPreview() {
            const option = achievementSelect.selectedOptions[0];
            const remaining = option ? Number(option.dataset.remaining || 0) : 0;
            const points = Math.min(Number(pointsInput.value || 0), remaining);
            document.getElementById('ratio-preview').innerText = '1 : '+ratio;
            document.getElementById('reduction-preview').innerText = points > 0 ? points * ratio+' poin' : '-';
            if (pointsInput.value > remaining) pointsInput.value = remaining;
        }
    </script>
@endsection
