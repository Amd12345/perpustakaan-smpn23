@extends('layouts.app')

@section('title', 'Ubah data siswa')

@section('content')
    <x-page-head title="Ubah data siswa" :subtitle="$student->name"
        :back="route('students.show', $student)" back-label="Data siswa" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('students.update', $student) }}">
                @csrf @method('PUT')

                <h2 class="card-section-title"><i class="bi bi-person-vcard" aria-hidden="true"></i> Identitas siswa</h2>

                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label" for="nisn">NISN</label>
                        <input type="text" name="nisn" id="nisn" class="form-control @error('nisn') is-invalid @enderror"
                            value="{{ old('nisn', $student->nisn) }}" required>
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Nama lengkap</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $student->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label" for="gender">Jenis kelamin</label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="L" @selected(old('gender', $student->gender) == 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender', $student->gender) == 'P')>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" for="class_id">Kelas</label>
                        <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id', $student->class_id) == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" for="academic_year_id">Tahun ajaran</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(old('academic_year_id', $student->academic_year_id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-6 col-md-4">
                        <label class="form-label" for="birth_date">Tanggal lahir</label>
                        <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                            value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
                        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="address">Alamat</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $student->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['aktif','nonaktif','lulus','pindah'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $student->status) == $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
                    <h2 class="card-section-title mb-0"><i class="bi bi-people" aria-hidden="true"></i> Orang tua / wali</h2>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-parent">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah
                    </button>
                </div>

                <div id="parents-container">
                    @php
                        $parents = old('parents', $student->orangTuas->map(fn ($parent) => [
                            'name' => $parent->name,
                            'phone' => $parent->phone,
                            'relation' => $parent->pivot->relation,
                            'is_primary' => (bool) $parent->pivot->is_primary,
                        ])->toArray());
                    @endphp

                    @foreach($parents as $index => $parent)
                        <div class="parent-row row g-2 align-items-end mb-2">
                            <div class="col-12 col-md-4">
                                <input type="text" name="parents[{{ $index }}][name]" class="form-control" placeholder="Nama"
                                    aria-label="Nama orang tua atau wali" value="{{ $parent['name'] ?? '' }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <input type="text" name="parents[{{ $index }}][phone]" class="form-control" placeholder="Nomor WhatsApp"
                                    aria-label="Nomor WhatsApp orang tua atau wali" value="{{ $parent['phone'] ?? '' }}">
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="parents[{{ $index }}][relation]" class="form-select" aria-label="Hubungan dengan siswa">
                                    @foreach(['ayah','ibu','wali','orang tua'] as $relation)
                                        <option value="{{ $relation }}" @selected(($parent['relation'] ?? 'orang tua') === $relation)>{{ ucfirst($relation) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="parents[{{ $index }}][is_primary]" value="1"
                                        aria-label="Jadikan kontak utama" @checked(!empty($parent['is_primary']))>
                                    <label class="form-check-label">Utama</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-1 d-flex justify-content-end">
                                <button type="button" class="btn-icon danger remove-parent" aria-label="Hapus baris orang tua">
                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if(count($parents) === 0)
                        <div class="parent-row row g-2 align-items-end mb-2">
                            <div class="col-12 col-md-4">
                                <input type="text" name="parents[0][name]" class="form-control" placeholder="Nama" aria-label="Nama orang tua atau wali">
                            </div>
                            <div class="col-12 col-md-3">
                                <input type="text" name="parents[0][phone]" class="form-control" placeholder="Nomor WhatsApp" aria-label="Nomor WhatsApp orang tua atau wali">
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="parents[0][relation]" class="form-select" aria-label="Hubungan dengan siswa">
                                    <option value="ayah">Ayah</option>
                                    <option value="ibu">Ibu</option>
                                    <option value="wali">Wali</option>
                                    <option value="orang tua" selected>Orang Tua</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="parents[0][is_primary]" value="1" aria-label="Jadikan kontak utama">
                                    <label class="form-check-label">Utama</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-1 d-flex justify-content-end">
                                <button type="button" class="btn-icon danger remove-parent" aria-label="Hapus baris orang tua">
                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-actions">
                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let parentIndex = {{ count($parents) }};
        document.getElementById('add-parent').addEventListener('click', function () {
            const container = document.getElementById('parents-container');
            const row = container.querySelector('.parent-row').cloneNode(true);
            row.querySelectorAll('input, select').forEach(function (element) {
                element.name = element.name.replace(/parents\[\d+\]/, 'parents[' + parentIndex + ']');
                if (element.type === 'checkbox') element.checked = false;
                else if (element.tagName !== 'SELECT') element.value = '';
            });
            parentIndex++;
            container.appendChild(row);
        });
        document.getElementById('parents-container').addEventListener('click', function (event) {
            const button = event.target.closest('.remove-parent');
            if (! button) return;
            const rows = document.querySelectorAll('#parents-container .parent-row');
            if (rows.length > 1) button.closest('.parent-row').remove();
        });
    </script>
@endsection
