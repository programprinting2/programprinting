<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpkItemUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $spkId,
        public int $spkItemId,
        public float $progressPct,
        public string $progressColor,
        public int $remaining,
        public string $satuan,
        public bool $isDone,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('manager-order');
    }

    public function broadcastAs(): string
    {
        return 'spk.item.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'spk_id' => $this->spkId,
            'spk_item_id' => $this->spkItemId,
            'progress_pct' => $this->progressPct,
            'progress_color' => $this->progressColor,
            'remaining' => $this->remaining,
            'satuan' => $this->satuan,
            'is_done' => $this->isDone,
        ];
    }
}