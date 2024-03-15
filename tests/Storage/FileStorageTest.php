<?php
namespace RateLimiter\Tests\Storage;

use PHPUnit\Framework\TestCase;
use RateLimiter\Storage\FileStorage;

class FileStorageTest extends TestCase
{
    private $fileStorage;
    private $dataFile;

    protected function setUp(): void
    {
        // 指定测试用的文件路径
        $this->dataFile = sys_get_temp_dir() . '/test_rate_limiter.data';
        $this->fileStorage = new FileStorage($this->dataFile);
    }

    protected function tearDown(): void
    {
        // 测试完成后删除测试文件
        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }
    }

    public function testSetAndGet()
    {
        $this->fileStorage->set('key1', 'value1');
        $this->assertEquals('value1', $this->fileStorage->get('key1'));
    }

    public function testExpire()
    {
        $this->fileStorage->set('key2', 'value2', 1); // 设置1秒过期
        sleep(2); // 等待超过1秒
        $this->assertFalse($this->fileStorage->get('key2'));
    }

    public function testListAndRemove()
    {
        $this->fileStorage->rPush('list1', 'item1');
        $this->fileStorage->rPush('list1', 'item2');
        $this->assertEquals(2, $this->fileStorage->lLen('list1'));
        $this->assertEquals(['item1', 'item2'], $this->fileStorage->lRange('list1', 0, -1));

        $this->fileStorage->lRem('list1', 1, 'item1');
        $this->assertEquals(1, $this->fileStorage->lLen('list1'));
    }
}