<?php
namespace RateLimiter\Tests;

use PHPUnit\Framework\TestCase;
use RateLimiter\TokenBucketRateLimiter;
use RateLimiter\Storage\FileStorage;

class TokenBucketRateLimiterTest extends TestCase
{
    private $storage;
    private $rateLimiter;

    public function setUp(): void
    {
        $this->storage = new FileStorage(sys_get_temp_dir() . '/test_rate_limiter.data');
        $this->rateLimiter = new TokenBucketRateLimiter($this->storage, 10, 5);
    }

    public function tearDown(): void
    {
        // 清理测试数据
        $this->storage->clear();
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