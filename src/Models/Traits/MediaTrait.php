<?php

declare(strict_types=1);

namespace AmoJo\Models\Traits;

trait MediaTrait
{
    private ?string $media = null;
    private ?int $mediaDuration = null;

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getMediaDuration(): ?int
    {
        return $this->mediaDuration;
    }

    public function setMediaDuration(int $mediaDuration): self
    {
        $this->mediaDuration = $mediaDuration;

        return $this;
    }
}
