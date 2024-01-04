<?php

namespace App\Repositories;

use App\Models\Video;

final class VideoRepository
{
    public function getAll()
    {
        return Video::query()->paginate(request()->per_page);
    }
}
