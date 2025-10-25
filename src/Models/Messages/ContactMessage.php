<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

final class ContactMessage extends AbstractMessage
{
    protected const TYPE = MessageType::CONTACT;

    protected ?string $name = null;
    protected ?string $phone = null;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    private function validate(): void
    {
        if ($this->getName() === null && $this->getPhone() === null) {
            throw new RequiredParametersMissingException('Name and/or Phone are required.');
        }
    }

    public function toPayload(): array
    {
        $this->validate();

        return [
            ...parent::toPayload(),
            'contact' => [
                'name' => $this->getName(),
                'phone' => $this->getPhone(),
            ],
        ];
    }
}
