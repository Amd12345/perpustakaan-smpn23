@extends('layouts.app')

@section('title', 'Ubah kelas')

@section('content')
    <x-page-head title="Ubah kelas" :subtitle="$class->name"
        :back="route('classes.index')" back-label="Data kelas" />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('classes.update', $class) }}">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="academic_year_id">Tahun ajaran</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror">
                            @foreach($years as $item)
                                <option value="{{ $item->id }}" @selected(old('academic_year_id', $class->academic_year_id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" for="name">Nama kelas</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $class->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label" for="homeroom_teacher_id">Wali kelas</label>
                        <select name="homeroom_teacher_id" id="homeroom_teacher_id" class="form-select @error('homeroom_teacher_id') is-invalid @enderror">
                            <option value="">- Tidak ada -</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('homeroom_teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
