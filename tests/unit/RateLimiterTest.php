<?php

namespace Tests\unit\Strategy;


use RateLimiter\RateLimiter;
use RateLimiter\Storage\Interfaces\StorageInterface;

class RateLimiterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {

    }

    protected function _after()
    {
    }

    public function testCreate()
    {
        $rateLimiter = new RateLimiter($this->getMemoryStorage(), 'default', 5, 60);

        $this->tester->assertInstanceOf($rateLimiter, RateLimiter::class);
    }



    private function getMemoryStorage(): StorageInterface
    {
        return new class implements StorageInterface {

            private $memory = [];

            public function __construct()
            {
            }

            public function set(string $key, string $value, int $ttl): bool
            {
                $this->memory[$key] = [
                    'value' => $value,
                    'ttl' => time() + $ttl,
                ];

                return true;
            }

            public function get(string $key): string
            {
                if($this->has($key)) {
                    return $this->memory[$key]['value'];
                }

                return '';
            }

            public function has(string $key): bool
            {
                return array_key_exists($key, $this->memory) && $this->memory[$key]['ttl'] <= time();
            }

            public function del(string $key): bool
            {
                unset($this->memory[$key]);
                return true;
            }
        };
    }
}