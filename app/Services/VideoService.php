<?php

namespace App\Services;

use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\MediaOpener;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

final class VideoService
{
    private MediaOpener $video;
    private string $originalPathFile;
    private string $resultPathFile;
    private float $logoWidth;
    private float $logoHeight;
    private float $logoLeft;
    private float $logoBottom;

    public function handle(string $pathFile): string
    {
        $this->originalPathFile = $pathFile;

        $this->setWatermarkProperties();
        $this->addWatermark();
        $this->deleteOriginalFile();

        return $this->resultPathFile;
    }

    private function setWatermarkProperties(): void
    {
        $this->video = FFMpeg::openUrl($this->originalPathFile);
        $videoStreams = $this->video->getStreams();

        foreach ($videoStreams as $stream) {
            if ($stream->isVideo()) {
                $width = min($stream->getDimensions()->getWidth(), $stream->getDimensions()->getHeight());

                $this->logoWidth  = $width / 3;
                $this->logoHeight = $this->logoWidth / 3;
                $this->logoLeft   = $this->logoHeight / 2;
                $this->logoBottom = $this->logoWidth / 2;
            }
        }
    }

    private function addWatermark(): void
    {
        $this->video->addWatermark(function (WatermarkFactory $watermark) {
            $watermark->fromDisk('local')
                ->open('logo.png')
                ->left($this->logoLeft)
                ->bottom($this->logoBottom)
                ->width($this->logoWidth)
                ->height($this->logoHeight);
        })->export()
            ->toDisk('public')
            ->inFormat(new X264)
            ->save($fileName = Str::uuid() . '.mp4');

        $this->resultPathFile = Storage::url(public_path($fileName));
    }

    private function deleteOriginalFile(): void
    {
        Storage::disk('local')->delete($this->originalPathFile);
    }
}
