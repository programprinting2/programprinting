<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SpkActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $spkId,
        public string $nomorSpk,
        public string $action,      
        public ?string $status = null,
        public ?string $pelanggan = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    protected function payload(): array
    {
        return [
            'spk_id'     => $this->spkId,
            'nomor_spk'  => $this->nomorSpk,
            'action'     => $this->action,
            'status'     => $this->status,
            'pelanggan'  => $this->pelanggan,
        ];
    }
}