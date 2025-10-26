<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

final class ConnectResponse implements ResponseInterface
{
    private string $accountUuId;
    private string $scopeId;
    private string $title;
    private string $hookApiVersion;
    private bool $isTimeWindowDisabled;

    public function __construct(
        string $accountUuId,
        string $scopeId,
        string $title,
        string $hookApiVersion,
        bool $isTimeWindowDisabled
    ) {
        $this->accountUuId = $accountUuId;
        $this->scopeId = $scopeId;
        $this->title = $title;
        $this->hookApiVersion = $hookApiVersion;
        $this->isTimeWindowDisabled = $isTimeWindowDisabled;
    }

    public function getAccountUuid(): string
    {
        return $this->accountUuId;
    }

    public function getScopeId(): string
    {
        return $this->scopeId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHookApiVersion(): string
    {
        return $this->hookApiVersion;
    }

    public function isTimeWindowDisabled(): bool
    {
        return $this->isTimeWindowDisabled;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string)($data['account_id'] ?? ''),
            (string)($data['scope_id'] ?? ''),
            (string)($data['title'] ?? ''),
            (string)($data['hook_api_version'] ?? 'v2'),
            (bool)($data['is_time_window_disabled'] ?? true)
        );
    }

    public function toArray(): array
    {
        return [
            'account_id' => $this->accountUuId,
            'scope_id' => $this->scopeId,
            'title' => $this->title,
            'hook_api_version' => $this->hookApiVersion,
            'is_time_window_disabled' => $this->isTimeWindowDisabled,
        ];
    }
}
