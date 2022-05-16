<?php

namespace App\Tests\unit;

use App\Tests\util\CustomTestCase;
use App\Util\Error;
use App\Util\RedisClient;
use Exception;

class RedisClientTest extends CustomTestCase
{
    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function testRedisError(): void
    {
        ['code' => $code, 'message' => $msg] = Error::REDIS_DISCONNECTED;
        $this->expectExceptionCode($code);
        $this->expectExceptionMessage($msg);

        RedisClient::getAdapter('localhost');
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function testRedisSuccess(): void
    {
        $client = RedisClient::getAdapter();
        $this->assertTrue($client->isConnected());
    }
}