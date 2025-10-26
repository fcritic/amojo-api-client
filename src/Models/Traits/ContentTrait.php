<?php

declare(strict_types=1);

namespace AmoJo\Models\Traits;

trait ContentTrait
{
    private ?string $media = null;
    private ?string $fileName = null;
    private ?int $fileSize = null;

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
