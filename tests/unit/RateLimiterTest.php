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

        $this->tester->assertInstanceOf(RateLimiter::class, $rateLimiter);
    }

    public function testMethodCheckSuccessful1() {
        $rateLimiter = new RateLimiter($this->getMemoryStorage(), 'default', 5, 3);

        $id = '127.0.0.1';

        for ($i=1;$i<=5;$i++) {
            //five requests passed OK
            $this->tester->assertTrue($rateLimiter->check($id));
        }
        //6 request out of limit
        $this->tester->assertFalse($rateLimiter->check($id));

        sleep(4);
        //reset. New period, new request limit.
        $this->tester->assertTrue($rateLimiter->check($id));
    }

    public function testMethodGetAllow() {
        $rateLimiter = new RateLimiter($this->getMemoryStorage(), 'default', 5, 3);

        $id = '127.0.0.1';

        $res = $rateLimiter->getAllow($id);

        $this->tester->assertEquals(5, $res);

        $rateLimiter->check($id);
        $res = $rateLimiter->getAllow($id);

        $this->tester->assertEquals(4, $res);
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
                return array_key_exists($key, $this->memory) && $this->memory[$key]['ttl'] >= time();
            }

            public function del(string $key): bool
            {
                unset($this->memory[$key]);
                return true;
            }
        };
    }
}