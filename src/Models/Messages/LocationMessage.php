<?php

declare(strict_types=1);

namespace AmoJo\Models\Messages;

use AmoJo\Enum\MessageType;
use AmoJo\Exception\RequiredParametersMissingException;

final class LocationMessage extends AbstractMessage
{
    protected const TYPE = MessageType::LOCATION;

    private ?float $lon = null;
    private ?float $lat = null;

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    private function validate(): void
    {
        if ($this->getLat() === null && $this->getLon() === null) {
            throw new RequiredParametersMissingException('Lat and lon cannot be null.');
        }
    }

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
