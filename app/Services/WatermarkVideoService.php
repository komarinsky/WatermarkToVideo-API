<?php

namespace App\Services;

use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\MediaOpener;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

final class WatermarkVideoService
{
    private MediaOpener $media;
    private Video $video;
    private string $newFileName;
    private float $logoWidth;
    private float $logoHeight;
    private float $logoLeft;
    private float $logoBottom;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function execute(): void
    {
        $this->generateNewFileName();
        $this->setWatermarkProperties();
        $this->createNewVideoFileWithWatermark();
        $this->deleteOriginalFile();
        $this->updateVideoProperties();
    }

    private function generateNewFileName(): void
    {
        $this->newFileName = Str::uuid() . '.mp4';
    }

    private function setWatermarkProperties(): void
    {
        $this->media = FFMpeg::open($this->video->path);

        foreach ($this->media->getStreams() as $stream) {
            if ($stream->isVideo()) {
                $width = min($stream->getDimensions()->getWidth(), $stream->getDimensions()->getHeight());

                $this->logoWidth  = $width / 3;
                $this->logoHeight = $this->logoWidth / 3;
                $this->logoLeft   = $this->logoHeight / 2;
                $this->logoBottom = $this->logoWidth / 2;
            }
        }
    }

    private function createNewVideoFileWithWatermark(): void
    {
        $this->media->addWatermark(function (WatermarkFactory $watermark) {
            $watermark->fromDisk('watermark')
                ->open('logo.png')
                ->left($this->logoLeft)
                ->bottom($this->logoBottom)
                ->width($this->logoWidth)
                ->height($this->logoHeight);
        })->export()
            ->toDisk('public')
            ->inFormat(new X264)
            ->save($this->newFileName);
    }

    private function deleteOriginalFile(): void
    {
        Storage::delete($this->video->path);
    }

    private function updateVideoProperties()
    {
        $this->video->update([
            'path' => $this->newFileName,
            'public_url' => Storage::disk('public')->url($this->newFileName),
            'with_watermark' => true,
        ]);
    }
}
