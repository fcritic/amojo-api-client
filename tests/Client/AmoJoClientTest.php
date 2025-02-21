<?php

declare(strict_types=1);

namespace Tests\Client;

use AmoJo\Client\AmoJoClient;
use AmoJo\Client\ApiGatewayInterface;
use AmoJo\DTO\ConnectResponse;
use AmoJo\DTO\CreateChatResponse;
use AmoJo\DTO\DeliveryResponse;
use AmoJo\DTO\DisconnectResponse;
use AmoJo\DTO\HistoryChatResponse;
use AmoJo\DTO\MessageResponse;
use AmoJo\DTO\ReactResponse;
use AmoJo\DTO\TypingResponse;
use AmoJo\Enum\ActionsType;
use AmoJo\Enum\DeliveryStatus;
use AmoJo\Enum\ErrorCode;
use AmoJo\Enum\HttpMethod;
use AmoJo\Exception\AmoJoException;
use AmoJo\Models\Channel;
use AmoJo\Models\Conversation;
use AmoJo\Models\Deliver;
use AmoJo\Models\Messages\TextMessage;
use AmoJo\Models\Payload;
use AmoJo\Models\Users\Receiver;
use AmoJo\Models\Users\Sender;
use AmoJo\Models\Users\ValueObject\UserProfile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * @extends TestCase
 */
class AmoJoClientTest extends TestCase
{
    /**
     * @var ApiGatewayInterface|(ApiGatewayInterface&object&MockObject)|(ApiGatewayInterface&MockObject)|(object&MockObject)|MockObject
     */
    private ApiGatewayInterface $gateway;

    /** @var AmoJoClient */
    private AmoJoClient $client;

    /** @var string */
    private const SCOPE_ID = 'f4afd704-a49b-4010-9311-06ef3d4ceed8_f36b8c48-ed97-4866-8aba-d55d429da86d';

    /** @var string */
    private const ACCOUNT_UID = 'f36b8c48-ed97-4866-8aba-d55d429da86d';

    /** @var string */
    private const CHANNEL_UID = 'f4afd704-a49b-4010-9311-06ef3d4ceed8';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->gateway = $this->createMock(ApiGatewayInterface::class);
        $channel = new Channel(self::CHANNEL_UID, '11c08dd7ba836ea9cfc03133b4813d');
        $this->client = new AmoJoClient($channel, [], 'ru');
        $this->setPrivateProperty($this->client, $this->gateway);
    }

    /**
     * @param object $object
     * @param $value
     * @return void
     */
    private function setPrivateProperty(object $object, $value): void
    {
        try {
            $reflection = new ReflectionClass($object);
            $property = $reflection->getProperty('gateway');
            $property->setAccessible(true);
            $property->setValue($object, $value);
        } catch (ReflectionException $e) {
            $this->fail("Failed to set private property 'gateway': " . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testConnect(): void
    {
        $expectedResponse = [
            'account_id'              => self::ACCOUNT_UID,
            'scope_id'                => self::SCOPE_ID,
            'title'                   => 'Test Channel',
            'hook_api_version'        => 'v2',
            'is_time_window_disabled' => true
        ];

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(self::CHANNEL_UID . ActionsType::CONNECT, $this->equalTo([
                'account_id'       => self::ACCOUNT_UID,
                'title'            => null,
                'hook_api_version' => 'v2'
            ]))
            ->willReturn($expectedResponse);

        $response = $this->client->connect(self::ACCOUNT_UID);

        $this->assertInstanceOf(ConnectResponse::class, $response);
        $this->assertEquals(self::ACCOUNT_UID, $response->getAccountUid());
        $this->assertEquals(self::SCOPE_ID, $response->getScopeId());
        $this->assertEquals('Test Channel', $response->getTitle());
        $this->assertEquals('v2', $response->getHookApiVersion());
        $this->assertTrue($response->isTimeWindowDisabled());
    }

    /**
     * @return void
     */
    public function testDisconnect(): void
    {
        $this->gateway->expects($this->once())
            ->method(HttpMethod::DELETE_REQUEST)
            ->with(self::CHANNEL_UID . ActionsType::DISCONNECT, $this->equalTo([
                'account_id' => self::ACCOUNT_UID,
            ]));

        $response = $this->client->disconnect(self::ACCOUNT_UID);
        $this->assertInstanceOf(DisconnectResponse::class, $response);
    }

    /**
     * @return void
     */
    public function testCreateChat(): void
    {
        $conversationRefId = 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4';
        $useNameRefId = '9320f3de-aa61-4d12-8cf8-39e91b347445';
        $conversationId = '12259256265';
        $userId = '3986893063';
        $userName = 'John Doe';
        $avatarUrl = 'https://example.com/avatar.png';
        $phone = '79110001133';

        $expectedResponse = [
            'id'   => $conversationRefId,
            'user' => [
                'id'        => $useNameRefId,
                'client_id' => $userId,
                'name'      => $userName,
                'avatar'    => $avatarUrl,
                'phone'     => $phone,
            ]
        ];

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(
                self::SCOPE_ID . ActionsType::CHAT,
                $this->equalTo(
                    [
                        'conversation_id' => $conversationId,
                        'user'            => [
                            'id'      => $userId,
                            'name'    => $userName,
                            'avatar'  => $avatarUrl,
                            'profile' => [
                                'phone' => $phone,
                            ]
                        ]
                    ]
                )
            )
            ->willReturn($expectedResponse);

        $response = $this->client->createChat(
            self::ACCOUNT_UID,
            (new Conversation())->setId($conversationId),
            (new Sender())
                ->setId($userId)
                ->setName($userName)
                ->setAvatar($avatarUrl)
                ->setProfile(
                    (new UserProfile())
                        ->setPhone($phone)
                )
        );

        $this->assertInstanceOf(CreateChatResponse::class, $response);
        $this->assertEquals($conversationRefId, $response->getConversationRefId());
        $this->assertEquals($useNameRefId, $response->getUser()->getRefId());
        $this->assertEquals($userId, $response->getUser()->getId());
        $this->assertEquals($userName, $response->getUser()->getName());
        $this->assertEquals($avatarUrl, $response->getUser()->getAvatar());
        $this->assertEquals($phone, $response->getUser()->getProfile()->getPhone());
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $externalId = 'source_123';
        $payload = $this->createPayload();

        $expectedResponse = [
            'new_message' => [
                'conversation_id' => 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4',
                'sender_id'       => 'cf46e2c9-51f8-45c6-ba07-5d95e0aa4990',
                'receiver_id'     => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                'msgid'           => '3f2fdc80-2619-48d9-9c12-c1ea27cfdf6a',
                'ref_id'          => 'msg-789'
            ]
        ];

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(
                self::SCOPE_ID,
                $this->callback(function ($data) use ($externalId) {
                        return $data['event_type'] === 'new_message'
                        && $data['payload']['source']['external_id'] === $externalId;
                })
            )
            ->willReturn($expectedResponse);

        $response = $this->client->sendMessage(self::ACCOUNT_UID, $payload, $externalId);

        $this->assertInstanceOf(MessageResponse::class, $response);
        $this->assertEquals('b52c987e-1ef0-4544-b4e7-6d2ba665f9e4', $response->getConversationRefId());
    }

    /**
     * @return void
     */
    public function testDeliverStatus(): void
    {
        $messageUid = '3f2fdc80-2619-48d9-9c12-c1ea27cfdf6a';
        $deliver = (new Deliver(DeliveryStatus::ERROR))
            ->setErrorCode(ErrorCode::CONVERSATION_CREATION_FAILED);

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(
                self::SCOPE_ID . '/' . $messageUid . ActionsType::DELIVERY_STATUS,
                $this->equalTo(
                    [
                        'msgid'           => $messageUid,
                        'delivery_status' => DeliveryStatus::ERROR,
                        'error_code'      => ErrorCode::CONVERSATION_CREATION_FAILED
                    ]
                )
            )
            ->willReturn(['status' => 200]);

        $response = $this->client->deliverStatus(self::ACCOUNT_UID, $messageUid, $deliver);
        $this->assertInstanceOf(DeliveryResponse::class, $response);
    }

    /**
     * @return void
     */
    public function testGetHistoryChat(): void
    {
        $conversationRefId = 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4';
        $query = ['offset' => 10, 'limit' => 50];

        $expectedResponse = [
            'messages' => [
                [
                    'timestamp'      => 1738864454,
                    'msec_timestamp' => 1738864454584,
                    'sender'         => [
                        'id'   => '76c6e590-b9e7-4882-9dc7-b64a5ed4f6d6',
                        'name' => 'Пользователь amoCRM'
                    ],
                    'receiver'       => [
                        'id'        => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                        'client_id' => '3986893063'
                    ],
                    'message'        => [
                        'id'        => 'fbf3fe89-143a-43af-aaae-49fa1cece1d8',
                        'type'      => 'text',
                        'text'      => 'How are you?',
                        'media'     => '',
                        'thumbnail' => '',
                        'file_name' => '',
                        'file_size' => 0
                    ]
                ],
                [
                    'timestamp'      => 1738861384,
                    'msec_timestamp' => 1738861384982,
                    'sender'         => [
                        'id'   => '76c6e590-b9e7-4882-9dc7-b64a5ed4f6d6',
                        'name' => 'Пользователь amoCRM'
                    ],
                    'receiver'       => [
                        'id'        => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                        'client_id' => '3986893063'
                    ],
                    'message'        => [
                        'id'        => '0c96aa74-4f9c-4950-ad1e-0644c8430c7e',
                        'type'      => 'text',
                        'text'      => 'Hello',
                        'media'     => '',
                        'thumbnail' => '',
                        'file_name' => '',
                        'file_size' => 0
                    ]
                ],
                [
                    'timestamp'      => 1738861350,
                    'msec_timestamp' => 1738861350000,
                    'sender'         => [
                        'id'        => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                        'client_id' => '3986893063'
                    ],
                    'message'        => [
                        'id'        => '53da915d-32a0-4b8a-891d-da331b51cfc0',
                        'client_id' => '1',
                        'type'      => 'text',
                        'text'      => 'Hello',
                        'media'     => '',
                        'thumbnail' => '',
                        'file_name' => '',
                        'file_size' => 0
                    ]
                ]
            ]
        ];

        $this->gateway->expects($this->once())
            ->method(HttpMethod::GET_REQUEST)
            ->with(
                self::SCOPE_ID . ActionsType::CHAT . '/' . $conversationRefId . ActionsType::GET_HISTORY,
                $query
            )
            ->willReturn($expectedResponse);

        $response = $this->client->getHistoryChat(self::ACCOUNT_UID, $conversationRefId, $query);
        $this->assertInstanceOf(HistoryChatResponse::class, $response);
        $this->assertEquals(
            '53da915d-32a0-4b8a-891d-da331b51cfc0',
            $response->getMessages()[2]->getMessage()->getRefUid()
        );
        $this->assertEquals('1', $response->getMessages()[2]->getMessage()->getUid());
    }

    /**
     * @return void
     */
    public function testTyping(): void
    {
        $conversation = (new Conversation())->setId('12259256265');
        $sender = (new Sender())->setId('3986893063');

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(
                self::SCOPE_ID . ActionsType::TYPING,
                $this->equalTo(
                    [
                        'conversation_id' => '12259256265',
                        'sender' => ['id' => '3986893063']
                    ]
                )
            )
            ->willReturn(['status' => 200]);

        $response = $this->client->typing(self::ACCOUNT_UID, $conversation, $sender);
        $this->assertInstanceOf(TypingResponse::class, $response);
    }

    /**
     * @return void
     */
    public function testReact(): void
    {
        $conversation = (new Conversation())->setId('12259256265');
        $sender = (new Sender())->setId('3986893063');
        $message = (new TextMessage())->setRefUid('fbf3fe89-143a-43af-aaae-49fa1cece1d8');

        $this->gateway->expects($this->once())
            ->method(HttpMethod::POST_REQUEST)
            ->with(
                self::SCOPE_ID . ActionsType::REACT,
                $this->equalTo(
                    [
                        'conversation_id' => '12259256265',
                        'id'              => 'fbf3fe89-143a-43af-aaae-49fa1cece1d8',
                        'user'            => ['id' => '3986893063'],
                        'type'            => 'react',
                        'emoji'           => '🍺'
                    ]
                )
            )
            ->willReturn(['status' => 200]);

        $response = $this->client->react(self::ACCOUNT_UID, $conversation, $sender, $message, '🍺');
        $this->assertInstanceOf(ReactResponse::class, $response);
    }

    /**
     * @return Payload
     */
    private function createPayload(): Payload
    {
        $conversation = (new Conversation())->setId('12259256265')->setRefId('b52c987e-1ef0-4544-b4e7-6d2ba665f9e4');
        $sender = (new Sender())->setRefId('cf46e2c9-51f8-45c6-ba07-5d95e0aa4990');
        $receiver = (new Receiver())
            ->setId('3986893063')
            ->setRefId('9320f3de-aa61-4d12-8cf8-39e91b347445')
            ->setName('John Doe')
            ->setAvatar('https://example.com/avatar.png');
        $message = (new TextMessage())->setText('Test')->setUid('msg-789');

        return (new Payload())
            ->setConversation($conversation)
            ->setSender($sender)
            ->setReceiver($receiver)
            ->setMessage($message);
    }

    /**
     * @return void
     */
    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(AmoJoException::class);
        $this->client->connect('invalid-uuid');
    }
}
