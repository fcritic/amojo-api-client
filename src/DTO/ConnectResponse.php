<?php

declare(strict_types=1);

namespace AmoJo\DTO;

use AmoJo\Models\Channel;

/**
 * DTO ответа на подключение канал к аккаунту
 *
 * @extends AbstractResponse
 */
final class ConnectResponse extends AbstractResponse
{
    /** @var string amojo_id подключенного аккаунта к каналу чатов */
    private string $accountUId;

    /** @var string amojo_id _ channel_id */
    private string $scopeUId;

    /** @var string Отображаемое название канала чатов */
    private string $title;

    /** @var string версия вебхука, на текущий момент актуальная v2 */
    private string $hookApiVersion;

    /** @var bool */
    private bool $isTimeWindowDisabled;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->accountUId = $data['account_id'];
        $this->scopeUId = $data['scope_id'];
        $this->title = $data['title'];
        $this->hookApiVersion = $data['hook_api_version'];
        $this->isTimeWindowDisabled = $data['is_time_window_disabled'];
    }

    /**
     * ID аккаунта в API чатов
     *
     * @return string
     */
    public function getAccountUid(): string
    {
        return $this->accountUId;
    }

    /**
     * Идентификатор подключения канала для конкретного аккаунта
     *
     * @return string
     */
    public function getScopeId(): string
    {
        return $this->scopeUId;
    }

    /**
     * Отображаемое название канала в подключаемом аккаунте
     *
     * @see Channel
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Версия формата хука, который будет приходить интеграции при исходящих сообщениях.
     * В настройках канала чата, для указанной версии должен быть прописан адрес хука
     *
     * @return string
     */
    public function getHookApiVersion(): string
    {
        return $this->hookApiVersion;
    }

    /**
     * @return bool
     */
    public function isTimeWindowDisabled(): bool
    {
        return $this->isTimeWindowDisabled;
    }
}
