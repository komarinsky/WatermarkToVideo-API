<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadVideoFileRequest;
use App\Services\WatermarkVideoService;

class VideoFileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UploadVideoFileRequest $request)
    {
        $path = $request->file('file')->store();

        $watermarkVideoService = new WatermarkVideoService($path);
        $watermarkVideoService->execute();

        return response()->json(['video_url' => $watermarkVideoService->getPublicUrlResult()]);
    }
}
