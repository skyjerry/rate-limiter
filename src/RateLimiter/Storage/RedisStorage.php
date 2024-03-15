<?php

namespace RateLimiter\Storage;

class RedisStorage implements StorageInterface
{
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function set(string $key, $value, int $ttl = 0)
    {
        $this->redis->set($key, $value);
        if ($ttl > 0) {
            $this->redis->expire($key, $ttl);
        }
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function lLen(string $key): int
    {
        return $this->redis->lLen($key);
    }

    public function lRange(string $key, int $start, int $stop): array
    {
        return $this->redis->lRange($key, $start, $stop);
    }

    public function lRem(string $key, int $count, $value): int
    {
        return $this->redis->lRem($key, $value, $count);
    }

    public function rPush(string $key, $value)
    {
        $this->redis->rPush($key, $value);
    }

    public function lIndex(string $key, int $index)
    {
        return $this->redis->lIndex($key, $index);
    }

    public function expire(string $key, int $seconds)
    {
        $this->redis->expire($key, $seconds);
    }

    public function clear()
    {
        $this->redis->flushAll();
    }
}