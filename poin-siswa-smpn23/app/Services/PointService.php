<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PointService
{
    public static function balance(Student|int $student, ?int $academicYearId = null, string $balanceType = 'pelanggaran'): int
    {
        $studentId = $student instanceof Student ? $student->id : $student;

        return (int) PointTransaction::query()
            ->where('student_id', $studentId)
            ->when($academicYearId, fn ($query) => $query->where('academic_year_id', $academicYearId))
            ->where('balance_type', $balanceType)
            ->sum('points');
    }

    public static function record(array $data): PointTransaction
    {
        $required = ['student_id', 'academic_year_id', 'type', 'balance_type', 'source_type', 'source_id', 'points'];

        foreach ($required as $field) {
            if (! array_key_exists($field, $data)) {
                throw new InvalidArgumentException("Field {$field} wajib diisi.");
            }
        }

        $points = (int) $data['points'];
        $balanceType = $data['balance_type'];

        if (! in_array($balanceType, ['pelanggaran', 'prestasi'])) {
            throw new InvalidArgumentException('balance_type tidak valid.');
        }

        return DB::transaction(function () use ($data, $points, $balanceType) {
            $before = self::balance($data['student_id'], $data['academic_year_id'], $balanceType);
            $after = $before + $points;

            if ($after < 0) {
                throw new RuntimeException('Saldo poin tidak mencukupi untuk transaksi ini.');
            }

            return PointTransaction::create([
                'student_id' => $data['student_id'],
                'academic_year_id' => $data['academic_year_id'],
                'type' => $data['type'],
                'balance_type' => $balanceType,
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
                'points' => $points,
                'balance_before' => $before,
                'balance_after' => $after,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'description' => $data['description'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now(),
            ]);
        });
    }

    public static function hasTransaction(string $sourceType, int $sourceId, string $type): bool
    {
        return PointTransaction::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('type', $type)
            ->exists();
    }
}
