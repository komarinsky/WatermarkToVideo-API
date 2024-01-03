<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadVideoFileRequest;
use App\Services\VideoService;

class VideoFileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UploadVideoFileRequest $request)
    {
        $path = $request->file('file')->store('videos');

        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . $path);

        $result = (new VideoService)->handle($filePath);

        return response()->json(['path' => $result]);
    }
}
