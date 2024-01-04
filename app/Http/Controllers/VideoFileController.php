<?php

namespace App\Http\Controllers;

use App\Actions\SaveVideoFileAction;
use App\Http\Requests\UploadVideoFileRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Repositories\VideoRepository;

class VideoFileController extends Controller
{
    public function __construct(
        private readonly VideoRepository $repository
    ) {}

    public function index()
    {
        return VideoResource::collection($this->repository->getAll());
    }

    public function store(UploadVideoFileRequest $request, SaveVideoFileAction $action)
    {
        return VideoResource::make($action()->fresh());
    }

    public function show(Video $video)
    {
        return VideoResource::make($video);
    }
}
