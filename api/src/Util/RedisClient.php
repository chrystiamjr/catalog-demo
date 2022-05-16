<?php

namespace App\Util;

use Exception;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisClient
{
    /**
     * @param string|null $url
     * @return Redis
     * @throws Exception
     */
    public static function getAdapter(?string $url = null): Redis
    {
        $redisUrl = $url ?? getenv('REDIS_HOST');
        try {
            $client = RedisAdapter::createConnection(
                "redis://$redisUrl"
            );

            if (!$client->isConnected()) {
                throw Error::message(Error::REDIS_DISCONNECTED);
            }

            return $client;
        } catch (Exception $e) {
            throw Error::message(Error::REDIS_DISCONNECTED);
        }
    }
}