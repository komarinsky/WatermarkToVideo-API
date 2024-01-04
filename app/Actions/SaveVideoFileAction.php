<?php

namespace App\Actions;

use App\Jobs\DoWatermarkVideoJob;
use App\Models\Video;

final class SaveVideoFileAction
{
    public function __invoke(): Video
    {
        $video = Video::create([
            'path' => request()->file('file')->store()
        ]);

        DoWatermarkVideoJob::dispatch($video);

        return $video;
    }
}
