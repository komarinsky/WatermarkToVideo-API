<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\WatermarkVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DoWatermarkVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Video $video
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new WatermarkVideoService($this->video))->execute();
    }
}
