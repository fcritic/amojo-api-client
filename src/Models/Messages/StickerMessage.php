<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

final class StickerMessage extends AbstractMessage
{
    protected const TYPE = MessageType::STICKER;

    private ?string $stickerId = null;

    public function getStickerId(): ?string
    {
        return $this->stickerId;
    }

    public function setStickerId(string $stickerId): self
    {
        $this->stickerId = $stickerId;

        return $this;
    }

    private function validate(): void
    {
        if ($this->stickerId === null) {
            throw new RequiredParametersMissingException('Required parameter sticker id is missing');
        }
    }

    public function toPayload(): array
    {
        $this->validate();

        return [
            ...parent::toPayload(),
            'sticker_id' => $this->getStickerId(),
        ];
    }
}
