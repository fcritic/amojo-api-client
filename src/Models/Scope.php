<?php

declare(strict_types=1);

namespace AmoJo\Models;

class Scope
{
    /** @var string Scope_id */
    private string $id;

    /** @var string amoJo_id аккаунта */
    private string $accountUid;

    /** @var string id канала чатов */
    private string $channelUid;

    public function __construct(string $id, string $accountUid, string $channelUid)
    {
        $this->id = $id;
        $this->accountUid = $accountUid;
        $this->channelUid = $channelUid;
    }

    public static function create(string $channelId, string $accountUid): self
    {
        return new self($channelId . '_' . $accountUid, $channelId, $accountUid);
    }

    public function getScopeId(): ?string
    {
        return $this->id;
    }

    public function getAccountUid(): ?string
    {
        return $this->accountUid;
    }

    public function getChannelUid(): ?string
    {
        return $this->channelUid;
    }
}
