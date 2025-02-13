<?php

declare(strict_types=1);

namespace AmoJo\Models\Traits;

/**
 * @uses AbstractMessage
 */
trait ContentTrait
{
    /** @var string|null */
    private ?string $media = null;

    /** @var string|null */
    private ?string $fileName = null;

    /** @var int|null */
    private ?int $fileSize = null;

    /**
     * Получения названия файла
     *
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Установка названия файла
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Получения размера файла, доступного по ссылке в поле media, в байтах
     *
     * @return int
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * Установка размера файла, доступного по ссылке в поле media, в байтах
     *
     * @param int $fileSize
     * @return $this
     */
    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }
}
