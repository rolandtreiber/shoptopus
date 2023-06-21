<?php

namespace App\Listeners;

use App\Events\UserInteraction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateLastSeenUser
{
    /**
     * Handle the event.
     */
    public function handle(UserInteraction $event): void
    {
        $user = Auth()->user();
        if (!$user && $event->modelType === User::class) {
            $user = User::find($event->modelId);
        }
        if ($user) {
            // If within 5 minutes, do not update
            if ($user->last_seen < Carbon::now()->subMinutes(5)) {
                DB::table('users')->where('id', '=', $user->id)->update(['last_seen' => Carbon::now()->format('Y-m-d H:i:s')]);
            }
            // TODO: Save the interaction data into a separate database.
        }
    }
}
