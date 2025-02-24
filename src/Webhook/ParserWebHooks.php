<?php

declare(strict_types=1);

namespace AmoJo\Webhook;

use AmoJo\Enum\WebHookType;
use AmoJo\Models\Messages\MessageFactory;
use AmoJo\Models\Messages\ReplyTo;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Exception\InvalidRequestWebHookException;
use AmoJo\Webhook\Traits\ConversationParserTrait;
use AmoJo\Webhook\Traits\UserParserTrait;
use AmoJo\Webhook\Traits\ValidationTrait;

class ParserWebHooks
{
    use UserParserTrait;
    use ConversationParserTrait;
    use ValidationTrait;

    /**
     * Принимает декодированный JSON вебхука и парсит его в зависимости от его типа
     *
     * @param array $data
     * @return AbstractWebHookEvent
     */
    public function parse(array $data): AbstractWebHookEvent
    {
        switch ($this->detectEventType($data)) {
            case WebHookType::MESSAGE:
                return $this->parseMessageEvent($data);

            case WebHookType::REACTION:
                return $this->parseReactionEvent($data);

            case WebHookType::TYPING:
                return $this->parseTypingEvent($data);

            default:
                throw new InvalidRequestWebHookException('Unknown webhook type');
        }
    }

    /**
     * Оправляет тип вебхука и валидирует на обязательные свойства
     *
     * @param array $data
     * @return string
     */
    private function detectEventType(array $data): string
    {
        $types = [
            WebHookType::MESSAGE => fn($d) => isset($d['message']),
            WebHookType::REACTION => fn($d) => isset($d['action']['reaction']),
            WebHookType::TYPING => fn($d) => isset($d['action']['typing'])
        ];

        foreach ($types as $type => $checker) {
            if ($checker($data)) {
                $this->validateStructure($data, $this->getValidationRules($type), "[{$type}] ");
                return $type;
            }
        }

        throw new InvalidRequestWebHookException('Cannot detect webhook type');
    }

    /**
     * Парсер исходящего сообщения
     *
     * @param array $data
     * @return OutgoingMessageEvent
     */
    private function parseMessageEvent(array $data): OutgoingMessageEvent
    {
        $msgData = $data['message']['message'];
        $replyTo = null;

        if (isset($msgData['reply_to'])) {
            $replyTo = (new ReplyTo())
                ->setReplyRefUid($msgData['reply_to']['message']['id'])
                ->setReplyUid($msgData['reply_to']['message']['msgid']);
        }

        return new OutgoingMessageEvent(
            $data['account_id'],
            $data['time'],
            $this->parseUser($data['message']['receiver'], Receiver::class),
            $this->parseUser($data['message']['sender'], Sender::class),
            $data['message']['source']['external_id'] ?? null,
            $this->parseConversation($data['message']['conversation']),
            $data['message']['timestamp'],
            $data['message']['msec_timestamp'],
            (new MessageFactory())->create($data['message']),
            $replyTo
        );
    }

    /**
     * Парсер реакции
     *
     * @param array $data
     * @return ReactionEvent
     */
    private function parseReactionEvent(array $data): ReactionEvent
    {
        $reactionData = $data['action']['reaction'];

        return new ReactionEvent(
            $data['account_id'],
            $data['time'],
            (new MessageFactory())->create($reactionData),
            $this->parseUser($reactionData['user'], Sender::class),
            $this->parseConversation($reactionData['conversation']),
            $reactionData['type'],
            $reactionData['emoji'] ?? null
        );
    }

    /**
     * Парсер печатания
     *
     * @param array $data
     * @return TypingEvent
     */
    private function parseTypingEvent(array $data): TypingEvent
    {
        $typingData = $data['action']['typing'];

        return new TypingEvent(
            $data['account_id'],
            $data['time'],
            $this->parseUser($typingData['user'], Sender::class),
            $this->parseConversation($typingData['conversation']),
            $data['action']['typing']['expired_at']
        );
    }
}
