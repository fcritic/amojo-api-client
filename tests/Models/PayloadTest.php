<?php

declare(strict_types=1);

namespace Tests\Models;

use AmoJo\Models\Conversation;
use AmoJo\Models\Messages\TextMessage;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Models\Users\ValueObject\UserProfile;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class PayloadTest extends TestCase
{
    /**
     * @return void
     */
    public function testFullPayload(): void
    {
        $conversation = (new Conversation())->setId('5454120498');

        $sender = (new Sender())
            ->setRefId('b52c987e-1ef0-4544-b4e7-6d2ba665f9e4')
            ->setName('John Doe');

        $receiver = (new Receiver())
            ->setId('6950671')
            ->setName('Alice Smith')
            ->setAvatar('https://example.com/avatar.jpg')
            ->setProfile(
                (new UserProfile())
                    ->setPhone('+123456789')
            );

        $message = (new TextMessage())
            ->setText('Hello World')
            ->setUid('1036')
            ->setTimestamp(1678901234);

        $payload = (new Payload())
            ->setConversation($conversation)
            ->setSender($sender)
            ->setReceiver($receiver)
            ->setMessage($message);

        $result = $payload->toApi();

        $this->assertEquals([
            'conversation_id'     => '5454120498',
            'msgid'               => '1036',
            'timestamp'           => 1678901234,
            'msec_timestamp'      => 1678901234000,
            'sender'              => [
                'ref_id' => 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4',
                'name'   => 'John Doe'
            ],
            'receiver'            => [
                'id'      => '6950671',
                'name'    => 'Alice Smith',
                'avatar'  => 'https://example.com/avatar.jpg',
                'profile' => [
                    'phone' => '+123456789'
                ]
            ],
            'message'             => [
                'type' => 'text',
                'text' => 'Hello World'
            ],
            'silent'              => false
        ], $result);
    }
}
