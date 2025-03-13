<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $context;

    /**
     * Create a new job instance.
     */
    public function __construct(string $message, array $context)
    {
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log
                ::channel('gateway')
                ->info($this->message, $this->context);
        } catch (\Exception $e) {
            Log
                ::channel('activity')
                ->error('Elasticsearch log failed: ' . $e->getMessage());
        }
    }
}
