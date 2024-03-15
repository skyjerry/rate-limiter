<?php
namespace RateLimiter;

use RateLimiter\Storage\StorageInterface;

class SlidingWindowRateLimiter implements RateLimiterInterface
{
    private StorageInterface $storage;
    private int $maxRequests; //单位时间窗口最大请求数
    private int $window; //时间窗口长度,单位秒

    public function __construct(StorageInterface $storage, int $maxRequests, int $window)
    {
        $this->storage = $storage;
        $this->maxRequests = $maxRequests;
        $this->window = $window;
    }

    public function acquire(string $key, int $permits = 1): bool
    {
        $microtime = microtime(true);
        $windowStart = $microtime - $this->window;

        //移除过期的请求记录
        $requests = $this->storage->lRange($key, 0, -1);
        foreach ($requests as $i => $ts) {
            if ($ts < $windowStart) {
                $this->storage->lRem($key, 1, $ts);
            } else {
                break;
            }
        }

        //判断请求数是否超限
        $count = $this->storage->lLen($key);
        if ($count + $permits > $this->maxRequests) {
            return false;
        }

        //记录本次请求
        $this->storage->rPush($key, $microtime);
        $this->storage->expire($key, $this->window);

        return true;
    }
}