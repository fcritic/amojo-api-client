<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * DTO ответа импорта/редактирования сообщения
 *
 * @extends AbstractResponse
 */
final class MessageResponse extends AbstractResponse
{
    /** @var string */
    private string $conversationRefId;

    /** @var string|null */
    private ?string $senderRefId;

    /** @var string|null */
    private ?string $receiverRefId;

    /** @var string */
    private string $msgRefId;

    /** @var string */
    private string $msgId;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->conversationRefId = $data['new_message']['conversation_id'];
        $this->senderRefId = $data['new_message']['sender_id'] ?? null;
        $this->receiverRefId = $data['new_message']['receiver_id'] ?? null;
        $this->msgRefId = $data['new_message']['msgid'];
        $this->msgId = $data['new_message']['ref_id'];
    }

    /**
     * Идентификатор чата в API чатов
     *
     * @return string
     */
    public function getConversationRefId(): string
    {
        return $this->conversationRefId;
    }

    /**
     * Идентификатор отправителя в API чатов
     * Не null если выполнен запрос на импорт сообщения, а не редактирование
     *
     * @return string|null
     */
    public function getSenderRefId(): ?string
    {
        return $this->senderRefId;
    }

    /**
     * Идентификатор получателя в API чатов
     * Не null если выполнен запрос на импорт исходящего сообщения
     *
     * @return string|null
     */
    public function getReceiverRefId(): ?string
    {
        return $this->receiverRefId;
    }

    /**
     * Идентификатор сообщения на стороне интеграции
     *
     * @return string
     */
    public function getMsgRefId(): string
    {
        return $this->msgRefId;
    }

    /**
     * Идентификатор сообщения в API чатов
     *
     * @return string
     */
    public function getMsgId(): string
    {
        return $this->msgId;
    }
}
