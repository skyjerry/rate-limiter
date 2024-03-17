<?php

namespace RateLimiter\Tests;

use PHPUnit\Framework\TestCase;
use RateLimiter\RateLimiterFactory;
use RateLimiter\SlidingWindowRateLimiter;
use RateLimiter\Storage\FileStorage;

class SlidingWindowRateLimiterTest extends TestCase
{
    private $rateLimiter;
    private $filePath;

    public function setUp(): void
    {
        $this->filePath = sys_get_temp_dir() . '/test_rate_limiter.data';
        $this->rateLimiter = RateLimiterFactory::createRateLimiter(
            'slidingWindow',
            'file',
            [
                'filePath' =>  $this->filePath,
            ],
            10,
            1
        );
    }

    public function tearDown(): void
    {
        (new FileStorage($this->filePath))->clear();
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