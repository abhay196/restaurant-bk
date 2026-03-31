<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogUserActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle(): void
    {
        // 💾 This is where the permanent insert happens
        DB::table('user_logs')->insert([
            'user_id'     => $this->details['user_id'],
            'name'        => $this->details['name'],
            'action'      => $this->details['action'],
            'action_time' => $this->details['time'],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}