<?php

namespace RateLimiter\Storage\Interfaces;

interface StorageInterface
{

    public function set(string $key, string $value, int $ttl): bool;

    public function get(string $key): string;

    public function has(string $key): bool;

    public function del(string $key): bool;
}