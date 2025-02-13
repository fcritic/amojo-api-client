<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

/**
 * @extends AbstractMessage
 */
final class ContactMessage extends AbstractMessage
{
    /** @var string */
    protected const TYPE = MessageType::CONTACT;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string|null
     */
    protected ?string $phone = null;

    /**
     * Установка имени контакта
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получение имени контакта
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установка телефона контакта
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Получение телефона контакта
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Валидация обязательный полей для типа сообщения
     *
     * @return void
     */
    private function validate(): void
    {
        if ($this->getName() === null && $this->getPhone() === null) {
            throw new RequiredParametersMissingException('Name and/or Phone are required.');
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
            'contact' => [
                'name'  => $this->getName(),
                'phone' => $this->getPhone(),
            ],
        ];
    }
}
