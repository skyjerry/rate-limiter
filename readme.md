# rate-limiter

[English](#english-version) | [中文](#中文版)

## 中文版

这是一个简单的 PHP 限流器类库,提供了令牌桶和滑动窗口两种限流算法,以及文件存储和 Redis 存储两种存储方式。

### 功能特性

- 支持令牌桶和滑动窗口两种限流算法，并支持扩展
- 支持文件存储和 Redis 存储两种存储方式，并支持扩展
- 限流规则可以针对全局或特定的资源 key
- 提供了 PSR-4 标准的自动加载机制
- 包含单元测试,保证代码质量

### 使用方法

1. 通过 Composer 安装:
    ```bash
    composer require skyjerry/rate-limiter
    ```
2. 在代码中使用:
    ```php
    use RateLimiter\TokenBucketRateLimiter;
    use RateLimiter\Storage\FileStorage;
    
    $storage = new FileStorage();
    $rateLimiter = new TokenBucketRateLimiter($storage, 100, 60);
    
    if (!$rateLimiter->acquire('user_1', 10)) {
        echo "限流了!";
    }
    ```

### 注意事项

- 这是一个 Demo 级别的项目,仅供学习参考,不建议用于生产环境。
- 在高并发场景下,建议使用 Redis 等内存型存储,以提高性能。
- 限流规则的设置需要根据实际业务需求进行调整,不同的场景可能需要不同的限流策略。

### 贡献指南

欢迎提交 Issue 和 Pull Request,帮助改进这个项目。在提交 PR 之前,请先运行单元测试,确保所有测试都能通过。

### English Version

This is a simple rate limiter library for PHP, providing two rate limiting algorithms: token bucket and sliding window, and two storage methods: file storage and Redis storage.

### Features

- Supports two rate limiting algorithms: token bucket and sliding window
- Supports two storage methods: file storage and Redis storage
- Rate limiting rules can be applied globally or to specific resource keys
- Provides PSR-4 standard autoloading mechanism
- Includes unit tests to ensure code quality

### Usage
1. Install via Composer:
    ```bash
    composer require skyjerry/rate-limiter
    ```
2. Use in your code:
    ```php
    use RateLimiter\TokenBucketRateLimiter;
    use RateLimiter\Storage\FileStorage;
    
    $storage = new FileStorage();
    $rateLimiter = new TokenBucketRateLimiter($storage, 100, 60);
    
    if (!$rateLimiter->acquire('user_1', 10)) {
        echo "Rate limited!";
    }
    ```

### Cautions

- This is a demo-level project, for learning reference only, not recommended for direct use in production environments.
- For high concurrency scenarios, it is recommended to use memory-based storage like Redis to improve performance.
- The setting of rate limiting rules needs to be adjusted according to actual business requirements, different scenarios may require different rate limiting strategies.


### Contribution Guide
Issues and Pull Requests are welcome to help improve this project. Before submitting a PR, please run the unit tests to ensure all tests pass.