<?php

declare(strict_types=1);

namespace Tests\Models;

use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Conversation;
use AmoJo\Models\Messages\TextMessage;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use PHPUnit\Framework\TestCase;

class PayloadValidationTest extends TestCase
{
    public function testMissingRequiredFields(): void
    {
        $this->expectException(AmoJoException::class);

        $payload = new Payload();
        $payload->toApi();
    }

    public function testInvalidReceiverForOutgoing(): void
    {
        $this->expectException(AmoJoException::class);

        $payload = (new Payload())
            ->setConversation(new Conversation())
            ->setSender(new Sender())
            ->setMessage(new TextMessage())
            ->setReceiver(new Receiver());

        $payload->toApi();
    }
}
