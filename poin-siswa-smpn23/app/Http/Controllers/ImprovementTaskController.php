<?php

namespace App\Http\Controllers;

use App\Models\ImprovementSubmission;
use App\Models\ImprovementTask;
use App\Models\Setting;
use App\Models\Violation;
use App\Services\ActivityLogService;
use App\Services\PointService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImprovementTaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = ImprovementTask::query()
            ->with(['violation.student.class', 'violation.category', 'teacher'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('improvements.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $violation = Violation::with('student.class')->findOrFail($request->input('violation'));

        if ($violation->status !== 'aktif') {
            return redirect()->route('violations.show', $violation)->with('error', 'Pelanggaran ini sudah dibatalkan.');
        }

        if ($violation->improvementTask) {
            return redirect()->route('improvements.show', $violation->improvementTask)
                ->with('error', 'Persyaratan penyelesaian sudah dibuat untuk pelanggaran ini.');
        }

        return view('improvements.create', compact('violation'));
    }

    public function store(Request $request)
    {
        $violation = Violation::with('student')->findOrFail($request->input('violation_id'));

        if ($violation->improvementTask) {
            return back()->with('error', 'Persyaratan penyelesaian sudah ada.');
        }

        $data = $request->validate([
            'violation_id' => ['required', 'exists:violations,id'],
            'description' => ['required', 'string'],
            'max_point_reduction' => ['required', 'integer', 'min:1', 'max:'.$violation->point_amount],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $task = DB::transaction(function () use ($data, $violation) {
            $task = ImprovementTask::create([
                'violation_id' => $violation->id,
                'teacher_id' => auth()->user()->teacher_id,
                'description' => $data['description'],
                'max_point_reduction' => $data['max_point_reduction'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'menunggu',
            ]);

            WhatsAppService::queueForStudent(
                $violation->student,
                'persyaratan',
                "Yth. Orang Tua/Wali {$violation->student->name},\n\n"
                ."Putra/i Anda memiliki pelanggaran ({$violation->category->name}, {$violation->point_amount} poin). "
                ."Untuk mengurangi poin, mohon menyelesaikan persyaratan berikut:\n\n"
                .$data['description']."\n\n"
                ."Maksimal pengurangan: {$data['max_point_reduction']} poin"
                .($data['due_date'] ? "\nBatas waktu: ".date('d M Y', strtotime($data['due_date'])) : '')
                ."\n\n- ".Setting::getValue('school_name', 'Sekolah'),
            );

            ActivityLogService::log(
                'create_improvement_task',
                $task,
                'Membuat persyaratan pengurangan poin untuk '.$violation->student->name,
            );

            return $task;
        });

        return redirect()->route('improvements.show', $task)
            ->with('success', 'Persyaratan penyelesaian dibuat dan notifikasi dikirim.');
    }

    public function show(ImprovementTask $task)
    {
        $task->load([
            'violation.student.class',
            'violation.category',
            'teacher',
            'approvedBy',
            'submissions.teacher',
            'submissions.reviewedBy',
        ]);

        return view('improvements.show', compact('task'));
    }

    public function storeSubmission(Request $request, ImprovementTask $task)
    {
        if (! in_array($task->status, ['menunggu', 'revisi', 'ditolak'])) {
            return back()->with('error', 'Tidak dapat mengunggah bukti pada status ini.');
        }

        $data = $request->validate([
            'evidence' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'note' => ['nullable', 'string'],
        ]);

        $path = $request->file('evidence')->store('improvements', 'public');

        DB::transaction(function () use ($task, $data, $path) {
            ImprovementSubmission::create([
                'improvement_task_id' => $task->id,
                'teacher_id' => auth()->user()->teacher_id,
                'evidence_path' => $path,
                'note' => $data['note'] ?? null,
                'status' => 'menunggu',
            ]);

            $task->update(['status' => 'menunggu_verifikasi', 'rejection_reason' => null]);

            ActivityLogService::log('submit_improvement_evidence', $task, 'Mengunggah bukti penyelesaian poin');
        });

        return back()->with('success', 'Bukti diunggah dan menunggu verifikasi.');
    }

    public function approve(Request $request, ImprovementTask $task)
    {
        if ($task->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Bukti belum masuk untuk diverifikasi.');
        }

        $data = $request->validate([
            'points' => ['required', 'integer', 'min:0', 'max:'.$task->max_point_reduction],
        ]);

        DB::transaction(function () use ($task, $data) {
            $transaction = PointService::record([
                'student_id' => $task->violation->student_id,
                'academic_year_id' => $task->violation->academic_year_id,
                'type' => 'pengurangan',
                'balance_type' => 'pelanggaran',
                'source_type' => ImprovementTask::class,
                'source_id' => $task->id,
                'points' => -$data['points'],
                'description' => 'Pengurangan poin: '.$task->description,
            ]);

            $task->update([
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $task->submissions()
                ->where('status', 'menunggu')
                ->update(['status' => 'disetujui', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);

            WhatsAppService::queueForStudent(
                $task->violation->student,
                'pengurangan',
                "Yth. Orang Tua/Wali {$task->violation->student->name},\n\n"
                ."Persyaratan penyelesaian pelanggaran telah disetujui. "
                ."Poin pelanggaran berkurang sebesar {$data['points']} poin. "
                ."Sisa poin pelanggaran saat ini: ".$transaction->balance_after." poin.\n\n"
                ."- ".Setting::getValue('school_name', 'Sekolah'),
            );

            ActivityLogService::log(
                'approve_improvement',
                $task,
                "Menyetujui pengurangan {$data['points']} poin untuk ".$task->violation->student->name,
            );
        });

        return back()->with('success', 'Pengurangan poin disetujui dan dicatat ke ledger.');
    }

    public function reject(Request $request, ImprovementTask $task)
    {
        if ($task->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Bukti belum masuk untuk diverifikasi.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $task->update([
            'status' => 'ditolak',
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $task->submissions()
            ->where('status', 'menunggu')
            ->update([
                'status' => 'ditolak',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => $data['rejection_reason'],
            ]);

        WhatsAppService::queueForStudent(
            $task->violation->student,
            'penolakan',
            "Yth. Orang Tua/Wali {$task->violation->student->name},\n\n"
            ."Bukti penyelesaian pelanggaran tidak disetujui dengan alasan:\n"
            .$data['rejection_reason']."\n\n"
            ."Silakan mengulangi persyaratan yang diberikan.\n\n"
            ."- ".Setting::getValue('school_name', 'Sekolah'),
        );

        ActivityLogService::log('reject_improvement', $task, 'Menolak bukti penyelesaian poin');

        return back()->with('success', 'Bukti ditolak dan notifikasi dikirim.');
    }

    public function revision(Request $request, ImprovementTask $task)
    {
        if ($task->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Revisi hanya dapat dilakukan setelah bukti masuk.');
        }

        $data = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $task->update(['status' => 'revisi', 'rejection_reason' => $data['note']]);

        ActivityLogService::log('revision_improvement', $task, 'Meminta revisi bukti penyelesaian');

        return back()->with('success', 'Status diubah menjadi revisi. Silakan unggah ulang bukti.');
    }
}
