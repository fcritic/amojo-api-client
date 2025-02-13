<?php

declare(strict_types=1);

namespace AmoJo\Models\Traits;

/**
 * @uses AbstractMessage
 */
trait MediaTrait
{
    /** @var string|null */
    private ?string $media = null;

    /** @var int|null */
    private ?int $mediaDuration = null;

    /**
     * Получение ссылки на картинку, файл, видео, аудио, голосовое сообщение или стикер в зависимости от типа сообщения.
     *
     *  Ссылка должна быть доступна для скачивания
     *
     * @return string|null
     */
    public function getMedia(): ?string
    {
        return $this->media;
    }

    /**
     * Установка ссылки на картинку, файл, видео, аудио, голосовое сообщение или стикер в зависимости от типа сообщения.
     *
     * Ссылка должна быть доступна для скачивания
     *
     * @param string $media
     * @return $this
     */
    public function setMedia(string $media): self
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Получение длительности для видео/аудио/голосовых сообщений
     *
     * @return int|null
     */
    public function getMediaDuration(): ?int
    {
        return $this->mediaDuration;
    }

    /**
     * Установка длительности для видео/аудио/голосовых сообщений
     *
     * @param int $mediaDuration
     * @return $this
     */
    public function setMediaDuration(int $mediaDuration): self
    {
        $this->mediaDuration = $mediaDuration;
        return $this;
    }
}
