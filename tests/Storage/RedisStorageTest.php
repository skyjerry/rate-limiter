<?php

namespace Storage;

use PHPUnit\Framework\TestCase;
use RateLimiter\Storage\RedisStorage;
use Redis;

class RedisStorageTest extends TestCase
{
    private $redisStorage;
    private $redis;

    protected function setUp(): void
    {
        // 初始化Redis客户端和RedisStorage实例
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redisStorage = new RedisStorage($this->redis);
    }

    protected function tearDown(): void
    {
        // 测试完成后清理Redis中的测试数据
        $this->redisStorage->clear();
    }

    public function testSetAndGet()
    {
        $this->redisStorage->set('key1', 'value1');
        $this->assertEquals('value1', $this->redisStorage->get('key1'));
    }

    public function testExpire()
    {
        $this->redisStorage->set('key2', 'value2', 1); // 设置1秒过期
        sleep(2); // 等待超过1秒
        $this->assertFalse($this->redisStorage->get('key2'));
    }

    public function testListAndRemove()
    {
        $this->redisStorage->rPush('list1', 'item1');
        $this->redisStorage->rPush('list1', 'item2');
        $this->assertEquals(2, $this->redisStorage->lLen('list1'));
        $this->assertEquals(['item1', 'item2'], $this->redisStorage->lRange('list1', 0, -1));

        $this->redisStorage->lRem('list1', 1, 'item1');
        $this->assertEquals(1, $this->redisStorage->lLen('list1'));
    }
}