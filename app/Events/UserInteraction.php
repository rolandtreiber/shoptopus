<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInteraction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $type,
        public string $modelType,
        public string $modelId)
    {

    }

}
