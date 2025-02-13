<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

/**
 * @extends AbstractMessage
 */
final class LocationMessage extends AbstractMessage
{
    /** @var string */
    protected const TYPE = MessageType::LOCATION;

    /** @var float|null */
    private ?float $lon = null;

    /** @var float|null */
    private ?float $lat = null;

    /**
     * Получения долготы
     *
     * @return float|null
     */
    public function getLon(): ?float
    {
        return $this->lon;
    }

    /**
     * Установка долготы
     *
     * @param float $lon
     * @return $this
     */
    public function setLon(float $lon): self
    {
        $this->lon = $lon;
        return $this;
    }

    /**
     * Получения широты
     *
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * Установка широты
     *
     * @param float $lat
     * @return $this
     */
    public function setLat(float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * Валидация обязательный полей для типа сообщения
     *
     * @return void
     */
    private function validate(): void
    {
        if ($this->getLat() === null && $this->getLon() === null) {
            throw new RequiredParametersMissingException('lat and lon cannot be null');
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
            'location' => [
                'lon' => $this->getLon(),
                'lat' => $this->getLat(),
            ],
        ];
    }
}
