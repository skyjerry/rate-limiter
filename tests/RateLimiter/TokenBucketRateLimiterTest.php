<?php
namespace RateLimiter\Tests;

use PHPUnit\Framework\TestCase;
use RateLimiter\RateLimiterFactory;
use RateLimiter\Storage\RedisStorage;
use Redis;

class TokenBucketRateLimiterTest extends TestCase
{
    private $rateLimiter;

    public function setUp(): void
    {
        $this->rateLimiter = RateLimiterFactory::createRateLimiter(
            'tokenBucket',
            'redis',
            [
                'host' =>  '127.0.0.1',
                'port' => 6379,
            ],
            10,
            5
        );
    }

    public function tearDown(): void
    {
        // 清理测试数据
        $redisClient = new Redis();
        $redisClient->connect('127.0.0.1', 6379);
        (new RedisStorage($redisClient))->clear();
    }

    public function testAcquireWithinLimit()
    {
        $result = $this->rateLimiter->acquire('api:/v1/ping', 5);
        $this->assertTrue($result);
    }

    public function testAcquireExceedLimit()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->rateLimiter->acquire('user:1');
        }
        $result = $this->rateLimiter->acquire('user:1', 1);
        $this->assertFalse($result);
    }

    public function testAcquireAfterRefill()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->rateLimiter->acquire('user:1');
        }
        sleep(3);
        $result = $this->rateLimiter->acquire('user:1', 5);
        $this->assertTrue($result);
    }
}