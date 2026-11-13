<?php

namespace App\Jobs;

use App\Models\WhatsAppNotification;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WhatsAppNotification $notification)
    {
    }

    public function handle(): void
    {
        app(WhatsAppService::class)->send($this->notification);
    }
}
