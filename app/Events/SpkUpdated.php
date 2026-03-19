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
        public int $spkProgressPct,
        public string $spkProgressColor, 
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
        return [
            'spk_id' => $this->spkId,
            'spk_progress_pct' => $this->spkProgressPct,
            'spk_progress_color' => $this->spkProgressColor,
        ];
    }
}