<?php

declare(strict_types=1);

namespace AmoJo\DTO\Response;

final class MessageResponse implements ResponseInterface
{
    private string $conversationRefId;
    private ?string $senderRefId;
    private ?string $receiverRefId;
    private string $msgRefId;
    private string $msgId;

    public function __construct(
        string $conversationRefId,
        string $senderRefId,
        string $receiverRefId,
        string $msgRefId,
        string $msgId
    ) {
        $this->conversationRefId = $conversationRefId;
        $this->senderRefId = $senderRefId;
        $this->receiverRefId = $receiverRefId;
        $this->msgRefId = $msgRefId;
        $this->msgId = $msgId;
    }

    /**
     * Идентификатор чата в API чатов
     */
    public function getConversationRefId(): string
    {
        return $this->conversationRefId;
    }

    /**
     * Идентификатор отправителя в API чатов
     * Не null если выполнен запрос на импорт сообщения, а не редактирование
     */
    public function getSenderRefId(): ?string
    {
        return $this->senderRefId;
    }

    /**
     * Идентификатор получателя в API чатов
     * Не null если выполнен запрос на импорт исходящего сообщения
     */
    public function getReceiverRefId(): ?string
    {
        return $this->receiverRefId;
    }

    /**
     * Идентификатор сообщения на стороне интеграции
     */
    public function getMsgRefId(): string
    {
        return $this->msgRefId;
    }

    /**
     * Идентификатор сообщения в API чатов
     */
    public function getMsgId(): string
    {
        return $this->msgId;
    }

    public static function fromArray(array $data): ResponseInterface
    {
        return new self(
            (string)($data['new_message']['conversation_id'] ?? ''),
            (string)($data['new_message']['sender_id'] ?? null),
            (string)($data['new_message']['receiver_id'] ?? null),
            (string)($data['new_message']['msgid'] ?? ''),
            (string)($data['new_message']['ref_id'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'new_message' => [
                'conversation_id' => $this->getConversationRefId(),
                'sender_id' => $this->getSenderRefId(),
                'receiver_id' => $this->getReceiverRefId(),
                'msg_id' => $this->getMsgRefId(),
                'ref_id' => $this->getMsgId(),
            ]
        ];
    }
}
