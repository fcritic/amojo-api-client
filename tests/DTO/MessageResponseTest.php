<?php

declare(strict_types=1);

namespace Tests\DTO;

use AmoJo\DTO\MessageResponse;
use AmoJo\DTO\ResponseFactory;
use AmoJo\Enum\ActionsType;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class MessageResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testMessageDataMapping(): void
    {
        $data = [
            'new_message' => [
                'conversation_id' => 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4',
                'sender_id'       => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                'msgid'           => 'c6af5fb1-5b00-48f7-98cf-95edb29c704d',
                'ref_id'          => '12323'
            ]
        ];

        /** @var MessageResponse $response */
        $response = ResponseFactory::create(ActionsType::MESSAGE, $data);

        $this->assertEquals('b52c987e-1ef0-4544-b4e7-6d2ba665f9e4', $response->getConversationRefId());
        $this->assertEquals('9320f3de-aa61-4d12-8cf8-39e91b347445', $response->getSenderRefId());
        $this->assertEquals(null, $response->getReceiverRefId());
        $this->assertEquals('c6af5fb1-5b00-48f7-98cf-95edb29c704d', $response->getMsgRefId());
        $this->assertEquals('12323', $response->getMsgId());
    }
}
