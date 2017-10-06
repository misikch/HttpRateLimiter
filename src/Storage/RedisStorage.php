<?php

namespace RateLimiter\Storage;


use RateLimiter\Storage\Interfaces\StorageInterface;
use Redis;

class RedisStorage implements StorageInterface
{

    /**
     * @var Redis
     */
    protected $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function set(string $key, string $value, int $ttl): bool
    {
        return $this->redis->set($key, $value, $ttl);
    }

    public function get(string $key): string
    {
        return $this->redis->get($key);
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }

    public function del(string $key): bool
    {
        return (bool)$this->redis->del($key);
    }
}
