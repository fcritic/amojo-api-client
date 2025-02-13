<?php

declare(strict_types=1);

namespace Tests\DTO;

use AmoJo\DTO\ConnectResponse;
use AmoJo\DTO\ResponseFactory;
use AmoJo\Enum\ActionsType;
use PHPUnit\Framework\TestCase;

/**
 * @extends TestCase
 */
class ConnectResponseTest extends TestCase
{
    /**
     * @return void
     */
    public function testConnectDataMapping(): void
    {
        $data = [
            'account_id'              => 'f36b8c48-ed97-4866-8aba-d55d429da86d',
            'scope_id'                => 'f4afd704-a49b-4010-9311-06ef3d4ceed8_f36b8c48-ed97-4866-8aba-d55d429da86d',
            'title'                   => 'Test Channel',
            'hook_api_version'        => 'v2',
            'is_time_window_disabled' => true
        ];

        /** @var ConnectResponse $response */
        $response = ResponseFactory::create(ActionsType::CONNECT, $data);

        $this->assertEquals('f36b8c48-ed97-4866-8aba-d55d429da86d', $response->getAccountUid());
        $this->assertEquals(
            'f4afd704-a49b-4010-9311-06ef3d4ceed8_f36b8c48-ed97-4866-8aba-d55d429da86d',
            $response->getScopeId()
        );
        $this->assertEquals('Test Channel', $response->getTitle());
        $this->assertEquals('v2', $response->getHookApiVersion());
        $this->assertTrue($response->isTimeWindowDisabled());
    }
}
