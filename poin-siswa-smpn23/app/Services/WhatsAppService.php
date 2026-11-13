<?php

namespace App\Services;

use App\Jobs\SendWhatsAppJob;
use App\Models\Setting;
use App\Models\Student;
use App\Models\WhatsAppNotification;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function queueForStudent(Student $student, string $type, string $message): ?WhatsAppNotification
    {
        $parent = $student->primaryOrangTua()->first();

        if (! $parent) {
            return null;
        }

        $notification = WhatsAppNotification::create([
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'type' => $type,
            'message' => $message,
            'recipient_phone' => $parent->phone,
            'status' => 'tertunda',
        ]);

        SendWhatsAppJob::dispatch($notification);

        return $notification;
    }

    public static function send(WhatsAppNotification $notification): void
    {
        $token = env('FONTE_API_KEY');
        $url = rtrim((string) env('FONTE_API_URL', 'https://api.fonnte.com'), '/');

        if (! $token) {
            $notification->update([
                'status' => 'gagal',
                'error' => 'Token Fonnte belum diatur di environment.',
            ]);

            return;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => $token])
                ->post($url.'/send', [
                    'target' => self::normalizePhone($notification->recipient_phone),
                    'message' => $notification->message,
                    'countryCode' => '62',
                ]);

            $payload = $response->json();

            if ($response->successful() && ($payload['status'] ?? false)) {
                $notification->update([
                    'status' => 'terkirim',
                    'sent_at' => now(),
                    'provider_message_id' => $payload['id'] ?? null,
                    'error' => null,
                ]);
            } else {
                $notification->update([
                    'status' => 'gagal',
                    'error' => $payload['reason'] ?? $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            $notification->update([
                'status' => 'gagal',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }

    public static function checkViolationThreshold(Student $student, int $academicYearId, int $balanceBefore, int $balanceAfter): ?WhatsAppNotification
    {
        $threshold = (int) Setting::getValue('wa_threshold', 0);

        if ($threshold <= 0 || $balanceAfter < $threshold) {
            return null;
        }

        $beforeMultiple = intdiv($balanceBefore, $threshold);
        $afterMultiple = intdiv($balanceAfter, $threshold);

        if ($afterMultiple <= $beforeMultiple) {
            return null;
        }

        $kelas = $student->class?->name ?? '-';

        return self::queueForStudent(
            $student,
            'pelanggaran_ambang',
            "Yth. Orang Tua/Wali {$student->name},\n\n"
            ."Kami informasikan bahwa total poin pelanggaran putra/i Anda ({kelas}) telah mencapai {$balanceAfter} poin "
            ."(melewati batas {$threshold} poin).\n\n"
            ."Mohon melakukan pembinaan dan kerjasama dengan sekolah.\n\n"
            ."- ".Setting::getValue('school_name', 'Sekolah'),
        );
    }
}
