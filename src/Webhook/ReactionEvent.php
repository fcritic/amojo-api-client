<?php

declare(strict_types=1);

namespace AmoJo\Webhook;

use AmoJo\Enum\WebHookType;
use AmoJo\Models\Conversation;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\UserInterface;

/**
 * События реакции на сообщения
 *
 * @extends AbstractWebHookEvent
 */
final class ReactionEvent extends AbstractWebHookEvent
{
    /** @var MessageInterface */
    protected MessageInterface $message;

    /** @var string */
    protected string $reactionType;

    /** @var string */
    protected string $emoji;

    /**
     * @param string $accountUid
     * @param int $time
     * @param MessageInterface $message
     * @param UserInterface $user
     * @param Conversation $conversation
     * @param string $reactionType
     * @param string $emoji
     */
    public function __construct(
        string $accountUid,
        int $time,
        MessageInterface $message,
        UserInterface $user,
        Conversation $conversation,
        string $reactionType,
        string $emoji
    ) {
        parent::__construct($accountUid, $time, $user, $conversation);

        $this->message = $message;
        $this->reactionType = $reactionType;
        $this->emoji = $emoji;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return WebHookType::REACTION;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage(): MessageInterface
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getReactionType(): string
    {
        return $this->reactionType;
    }

    /**
     * @return string|null
     */
    public function getEmoji(): string
    {
        return $this->emoji;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
          ...parent::toArray(),
          'action' => [
              $this->getType() => [
                  'message' => $this->getMessage()->toArrayForWebHook(),
                  'user' => $this->getInitiator()->toPayload(),
                  'conversation' => $this->getConversation()->toArray(),
                  'type' => $this->getReactionType(),
                  'emoji' => $this->getEmoji(),
              ],
          ]
        ];
    }
}
