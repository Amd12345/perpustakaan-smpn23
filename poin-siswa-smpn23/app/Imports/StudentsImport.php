<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $errors = [];
        $activeYearId = (int) Setting::getValue('active_academic_year_id', 0);

        foreach ($rows as $index => $row) {
            $baris = $index + 2;
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));

            if ($nisn === '' || $nama === '') {
                $errors[] = "Baris {$baris}: NISN atau nama masih kosong.";
                continue;
            }

            try {
                DB::transaction(function () use ($row, $nisn, $nama, $activeYearId) {
                    $yearName = trim((string) ($row['tahun_ajaran'] ?? ''));
                    $year = AcademicYear::where('name', $yearName)->first();

                    if (! $year && $activeYearId) {
                        $year = AcademicYear::find($activeYearId);
                    }

                    if (! $year) {
                        throw new \RuntimeException('Tahun ajaran tidak ditemukan.');
                    }

                    $className = trim((string) ($row['kelas'] ?? ''));
                    $class = Kelas::where('academic_year_id', $year->id)
                        ->where('name', $className)
                        ->first();

                    if (! $class) {
                        throw new \RuntimeException("Kelas {$className} tidak ditemukan pada tahun ajaran {$year->name}.");
                    }

                    $gender = strtoupper(substr(trim((string) ($row['jenis_kelamin'] ?? '')), 0, 1));

                    if (! in_array($gender, ['L', 'P'])) {
                        throw new \RuntimeException('Jenis kelamin harus diisi L atau P.');
                    }

                    $student = Student::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'name' => $nama,
                            'class_id' => $class->id,
                            'academic_year_id' => $year->id,
                            'gender' => $gender,
                            'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? null),
                            'address' => trim((string) ($row['alamat'] ?? '')) ?: null,
                            'status' => 'aktif',
                        ],
                    );

                    $parentName = trim((string) ($row['nama_orang_tua'] ?? ''));
                    $parentPhone = trim((string) ($row['telepon_orang_tua'] ?? ''));

                    if ($parentName !== '' || $parentPhone !== '') {
                        $normalizedPhone = preg_replace('/\D/', '', $parentPhone);
                        $parent = OrangTua::firstOrNew(['phone' => $normalizedPhone ?: $parentPhone]);

                        $parent->name = $parentName !== '' ? $parentName : $parent->name;
                        $parent->phone = $normalizedPhone ?: $parentPhone;
                        $parent->save();

                        DB::table('student_parents')
                            ->where('student_id', $student->id)
                            ->where('parent_id', '!=', $parent->id)
                            ->update(['is_primary' => false]);

                        $student->orangTuas()->syncWithoutDetaching([
                            $parent->id => [
                                'relation' => trim((string) ($row['hubungan'] ?? '')) ?: 'orang tua',
                                'is_primary' => true,
                            ],
                        ]);
                    }
                });
            } catch (Throwable $exception) {
                $errors[] = 'Baris '.$baris.': '.$exception->getMessage();
            }
        }

        if (! empty($errors)) {
            throw new \RuntimeException(implode("\n", array_slice($errors, 0, 15)));
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return now()->instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
        }

        return date('Y-m-d', strtotime((string) $value));
    }
}
