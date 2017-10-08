<?php

namespace RateLimiter;

use RateLimiter\Storage\Interfaces\StorageInterface;

class RateLimiter
{

    /**
     * @var StorageInterface
     */
    protected $storage;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var int
     */
    protected $maxRequests;
    /**
     * @var int
     */
    protected $ttl;
    /**
     * @var int
     */
    protected $period;

    /**
     * RateLimiter constructor.
     * @param StorageInterface $storage
     * @param string $name
     * @param int $maxRequests
     * @param int $period in seconds
     */
    public function __construct(StorageInterface $storage, string $name, int $maxRequests, int $period)
    {
        $this->storage = $storage;
        $this->name = $name;
        $this->maxRequests = $maxRequests;
        $this->period = $period;
        $this->ttl = $this->period;
    }

    public function getAllow($id): int
    {
        $allowKey = $this->keyAllow($id);
        if ($this->storage->has($allowKey)) {
            return $this->storage->get($allowKey);
        }

        return $this->maxRequests;
    }

    public function keyAllow($id): string
    {
        return $this->name . ":" . $id . ":allow";
    }

    /**
     * Rate Limiting
     *
     * @param string $id
     * @return bool
     */
    public function check($id): bool
    {
        $timeKey = $this->keyTime($id);
        $allowKey = $this->keyAllow($id);

        if ($this->storage->has($timeKey)) {
            $currentTime = time();

            $timePassed = $currentTime - $this->storage->get($timeKey);
            $allow = $this->storage->get($allowKey);
            $allow--;

            if ($allow >= 0) {
                $this->storage->set($allowKey, $allow, $this->ttl);
                return true;
            }

            if ($timePassed <= $this->period) {
                return false;
            }
        }

        $this->initCounter($id);
        return true;
    }

    public function keyTime($id): string
    {
        return $this->name . ":" . $id . ":time";
    }

    /**
     * Purge rate limit record for $id
     * @param string $id
     */
    public function purge($id)
    {
        $this->storage->del($this->keyTime($id));
        $this->storage->del($this->keyAllow($id));
    }

    public function getMaxRequests(): int
    {
        return $this->maxRequests;
    }

    protected function initCounter(string $id)
    {
        $this->storage->set($this->keyTime($id), time(), $this->ttl);
        $this->storage->set($this->keyAllow($id), $this->maxRequests - 1, $this->ttl);
    }


}