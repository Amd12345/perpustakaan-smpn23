<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements Export, FromCollection, WithHeadings, WithMapping {
    public function collection(): \Illuminate\Support\Enumerable
    {
        return Student::query()
            ->with('class', 'academicYear')
            ->withSum(['pointTransactions as violation_points' => fn ($query) => $query->where('balance_type', 'pelanggaran')], 'points')
            ->withSum(['pointTransactions as achievement_points' => fn ($query) => $query->where('balance_type', 'prestasi')], 'points')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama',
            'Jenis Kelamin',
            'Kelas',
            'Tahun Ajaran',
            'Tanggal Lahir',
            'Alamat',
            'Status',
            'Poin Pelanggaran',
            'Poin Prestasi',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->name,
            $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $student->class?->name,
            $student->academicYear?->name,
            $student->birth_date?->format('d-m-Y'),
            $student->address,
            ucfirst($student->status),
            (int) ($student->violation_points ?? 0),
            (int) ($student->achievement_points ?? 0),
        ];
    }
}
