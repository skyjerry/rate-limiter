<?php
namespace RateLimiter;

use RateLimiter\Storage\StorageInterface;

class TokenBucketRateLimiter implements RateLimiterInterface
{
    private StorageInterface $storage;
    private int $maxPermits; //桶容量
    private float $rate; //令牌产生速率,单位秒

    public function __construct(StorageInterface $storage, int $maxPermits, float $rate)
    {
        $this->storage = $storage;
        $this->maxPermits = $maxPermits;
        $this->rate = $rate;
    }

    public function acquire(string $key, int $permits = 1): bool
    {
        $microtime = microtime(true);

        // 获取存储的令牌数，如果不存在则初始化为最大令牌数
        $storedPermits = $this->storage->get($key) ?? $this->maxPermits;
        $lastTimestamp = $this->storage->get($key.'_ts') ?? $microtime;

        // 计算新令牌数
        $newPermits = $storedPermits + ($microtime - $lastTimestamp) * $this->rate;
        if ($newPermits > $this->maxPermits) {
            $newPermits = $this->maxPermits;
        }

        // 判断令牌是否足够
        if ($newPermits < $permits) {
            return false;
        }

        // 更新令牌数和时间戳
        $this->storage->set($key, $newPermits - $permits);
        $this->storage->set($key.'_ts', $microtime);

        return true;
    }
}