<?php

namespace RateLimiter;

use RateLimiter\Storage\StorageFactory;

class RateLimiterFactory
{
    private static array $strategies = [
        'tokenBucket' => TokenBucketRateLimiter::class,
        'slidingWindow' => SlidingWindowRateLimiter::class,

        // 可以在下面添加更多策略...
    ];

    public static function createRateLimiter(string $rateLimitStrategy, $storageStrategy, $storageConfig, $maxPermits, $rate): RateLimiterInterface
    {
        if (!isset(self::$strategies[$rateLimitStrategy])) {
            throw new \InvalidArgumentException("Unsupported rate limiter strategy: " . $rateLimitStrategy);
        }

        // 获取策略对应的类
        $rateLimiterClass = self::$strategies[$rateLimitStrategy];
        // 根据配置创建存储实例
        $storage = StorageFactory::createStorage($storageStrategy, $storageConfig);

        return new $rateLimiterClass($storage, $maxPermits, $rate);
    }
}