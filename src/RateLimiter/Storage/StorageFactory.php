<?php

namespace RateLimiter\Storage;

class StorageFactory
{
    public static function createStorage($type, $config): StorageInterface
    {
        switch ($type) {
            case 'redis':
                $redis = new \Redis();
                $redis->connect($config['host'], $config['port']);
                return new RedisStorage($redis);
            case 'file':
                return new FileStorage($config['filePath']);
            default:
                throw new \InvalidArgumentException("Unsupported storage type: " . $type);
        }
    }
}