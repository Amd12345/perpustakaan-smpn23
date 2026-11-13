<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppNotification;
use App\Services\ActivityLogService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = WhatsAppNotification::query()
            ->with(['student', 'parent'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('nisn', 'like', '%'.$request->input('search').'%'));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function retry(WhatsAppNotification $notification)
    {
        if ($notification->status === 'terkirim') {
            return back()->with('error', 'Notifikasi ini sudah terkirim.');
        }

        WhatsAppService::send($notification);

        ActivityLogService::log('retry_notification', $notification, 'Mengirim ulang notifikasi WhatsApp');

        $status = $notification->fresh()->status;

        return back()->with(
            $status === 'terkirim' ? 'success' : 'error',
            $status === 'terkirim' ? 'Notifikasi berhasil dikirim ulang.' : 'Pengiriman ulang gagal: '.$notification->fresh()->error,
        );
    }
}
