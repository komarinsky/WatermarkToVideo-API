<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadVideoFileRequest;
use App\Services\WatermarkVideoService;

class VideoFileController extends Controller
{
    public function __construct(
        private readonly WatermarkVideoService $videoService
    ) {}

    public function __invoke(UploadVideoFileRequest $request)
    {
        $path = $request->file('file')->store();

        $this->videoService->execute($path);

        return response()->json(['video_url' => $this->videoService->getPublicUrlResult()]);
    }
}
