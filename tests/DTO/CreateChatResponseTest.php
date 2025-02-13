<?php

declare(strict_types=1);

namespace Tests\DTO;

use AmoJo\DTO\CreateChatResponse;
use AmoJo\DTO\ResponseFactory;
use AmoJo\Enum\ActionsType;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class CreateChatResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateChatDataMapping(): void
    {
        $data = [
            'id'   => 'b52c987e-1ef0-4544-b4e7-6d2ba665f9e4',
            'user' => [
                'id'        => '9320f3de-aa61-4d12-8cf8-39e91b347445',
                'client_id' => '3986893063',
                'name'      => 'John Doe',
                'avatar'    => 'https://test-sender-avatar',
                'phone'     => '79110001133',
            ]
        ];

        /** @var CreateChatResponse $response */
        $response = ResponseFactory::create(ActionsType::CHAT, $data);

        $this->assertEquals('b52c987e-1ef0-4544-b4e7-6d2ba665f9e4', $response->getConversationRefId());
        $this->assertEquals('9320f3de-aa61-4d12-8cf8-39e91b347445', $response->getUser()->getRefId());
        $this->assertEquals('3986893063', $response->getUser()->getId());
        $this->assertEquals('John Doe', $response->getUser()->getName());
        $this->assertEquals('https://test-sender-avatar', $response->getUser()->getAvatar());
        $this->assertEquals('', $response->getUser()->getProfileLink());
        $this->assertEquals('79110001133', $response->getUser()->getProfile()->getPhone());
        $this->assertEquals('', $response->getUser()->getProfile()->getEmail());
    }
}
