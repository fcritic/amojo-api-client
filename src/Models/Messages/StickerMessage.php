<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

/**
 * @extends AbstractMessage
 */
final class StickerMessage extends AbstractMessage
{
    /** @var string */
    protected const TYPE = MessageType::STICKER;

    /** @var string|null */
    private ?string $stickerId = null;

    /**
     * Получения универсального для всех аккаунтов идентификатора посылаемого стикера
     *
     * @return string|null
     */
    public function getStickerId(): ?string
    {
        return $this->stickerId;
    }

    /**
     * Установка универсального для всех аккаунтов идентификатора посылаемого стикера
     *
     * @param string $stickerId
     * @return $this
     */
    public function setStickerId(string $stickerId): self
    {
        $this->stickerId = $stickerId;
        return $this;
    }

    /**
     * Валидация обязательный полей для типа сообщения
     *
     * @return void
     */
    private function validate(): void
    {
        if ($this->stickerId === null) {
            throw new RequiredParametersMissingException('Required parameter sticker id is missing');
        }
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        $this->validate();

        return [
            ...parent::toPayload(),
            'sticker_id' => $this->getStickerId(),
        ];
    }
}
