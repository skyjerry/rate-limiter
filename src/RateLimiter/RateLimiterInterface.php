<?php
namespace RateLimiter;

interface RateLimiterInterface
{
    /**
     * 获取令牌,超出限制时会被阻塞
     * @param string $key 限制对象的key
     * @param int $permits 需要获取的令牌数量
     * @return bool
     */
    public function acquire(string $key, int $permits = 1): bool;
}