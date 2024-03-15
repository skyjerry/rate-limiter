<?php

namespace RateLimiter\Storage;

interface StorageInterface
{
    public function set(string $key, $value, int $ttl = 0);
    public function get(string $key);
    public function lLen(string $key): int;
    public function lRange(string $key, int $start, int $stop): array;
    public function lRem(string $key, int $count, $value): int;
    public function rPush(string $key, $value);
    public function lIndex(string $key, int $index);
    public function expire(string $key, int $seconds);
    public function clear();
}