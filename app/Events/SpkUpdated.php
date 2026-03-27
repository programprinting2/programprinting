<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpkUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $spkId,
        public ?int $spkProgressPct = null,
        public ?string $spkProgressColor = null,
        public ?string $status = null,
        public ?string $statusPembayaran = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('manager-order');
    }

    public function broadcastAs(): string
    {
        return 'spk.updated';
    }

    public function broadcastWith(): array
    {
        $payload = [
            'spk_id' => $this->spkId,
        ];

        if ($this->spkProgressPct !== null) {
            $payload['spk_progress_pct'] = $this->spkProgressPct;
        }

        if ($this->spkProgressColor !== null) {
            $payload['spk_progress_color'] = $this->spkProgressColor;
        }

        if ($this->status !== null) {
            $payload['status'] = $this->status;
        }

        if ($this->statusPembayaran !== null) {
            $payload['status_pembayaran'] = $this->statusPembayaran;
        }

        return $payload;
    }
}