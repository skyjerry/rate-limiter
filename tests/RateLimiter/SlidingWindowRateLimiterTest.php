<?php
namespace RateLimiter\Tests;

use PHPUnit\Framework\TestCase;
use RateLimiter\SlidingWindowRateLimiter;
use RateLimiter\Storage\FileStorage;

class SlidingWindowRateLimiterTest extends TestCase
{
    private $storage;
    private $rateLimiter;

    public function setUp(): void
    {
        $this->storage = new FileStorage(sys_get_temp_dir() . '/test_rate_limiter.data');
        $this->rateLimiter = new SlidingWindowRateLimiter($this->storage, 10, 1);
    }

    public function tearDown(): void
    {
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

    public function testAcquireAfterWindowExpired()
    {
        for ($i = 0; $i < 10; $i++) {
            $this->rateLimiter->acquire('user:1');
        }
        sleep(2);
        $result = $this->rateLimiter->acquire('user:1');
        $this->assertTrue($result);
    }
}