<?php

namespace RateLimiter\Storage;

class FileStorage implements StorageInterface
{
    private $dataFile = '/tmp/rate_limiter.data';
    private $data = [];

    public function __construct(string $dataFile = '')
    {
        if ($dataFile) {
            $this->dataFile = $dataFile;
        }
        if (is_file($this->dataFile)) {
            $this->data = json_decode(file_get_contents($this->dataFile), true);
        }
    }

    public function set(string $key, $value, int $ttl = 0)
    {
        $this->data[$key] = $value;
        if ($ttl > 0) {
            $this->data['__expires'][$key] = time() + $ttl;
        }
        $this->saveData();
    }

    public function get(string $key)
    {
        // 首先检查键是否存在于过期时间数组中，并且是否已经过期
        if (isset($this->data['__expires'][$key]) && time() > $this->data['__expires'][$key]) {
            // 如果键已经过期，从数据和过期时间数组中移除它，然后保存更改
            unset($this->data[$key], $this->data['__expires'][$key]);
            $this->saveData();
            return false;
        }
        // 如果键没有过期，返回它的值
        return $this->data[$key] ?? false;
    }

    public function lLen(string $key): int
    {
        return count($this->data[$key] ?? []);
    }

    public function lRange(string $key, int $start, int $stop): array
    {
        if (!isset($this->data[$key])) {
            return [];
        }
        if ($stop === -1) {
            $stop = count($this->data[$key]) - 1;
        }
        return array_slice($this->data[$key], $start, $stop - $start + 1);
    }

    public function lRem(string $key, int $count, $value): int
    {
        if (!isset($this->data[$key])) {
            return 0;
        }
        $removed = 0;
        foreach ($this->data[$key] as $i => $v) {
            if ($v === $value) {
                unset($this->data[$key][$i]);
                $removed++;
                if ($count > 0 && $removed >= $count) {
                    break;
                }
            }
        }
        $this->data[$key] = array_values($this->data[$key]);
        $this->saveData();
        return $removed;
    }

    public function rPush(string $key, $value)
    {
        $this->data[$key][] = $value;
        $this->saveData();
    }

    public function lIndex(string $key, int $index)
    {
        return $this->data[$key][$index] ?? false;
    }

    public function expire(string $key, int $seconds)
    {
        $this->data['__expires'][$key] = time() + $seconds;
        $this->saveData();
    }

    private function saveData()
    {
        $expires = $this->data['__expires'] ?? [];
        foreach ($expires as $key => $time) {
            if (time() > $time) {
                unset($this->data[$key], $expires[$key]);
            }
        }
        $this->data['__expires'] = $expires;
        file_put_contents($this->dataFile, json_encode($this->data));
    }

    public function clear()
    {
        $this->data = [];
        $this->saveData();;
    }
}