# HTTP Request Limiter

### Status
[![Build Status](https://travis-ci.org/misikch/HttpRateLimiter.svg?branch=master)](https://travis-ci.org/misikch/HttpRateLimiter)

## Requirements

* PHP >= 7.0

## Example:

Configure RateLimiter object:

```php
/**
 * Application bootstap file example. Create and configure RateLimiter object
 */
$app->getContainer()->add('httpRateLimiter', function() use ($config) {
    $redis = new Redis();
    $storage = new \RateLimiter\Storage\RedisStorage($redis);
   return new RateLimiter\RateLimiter($storage, $config['name'], $config['maxRequests'], $config['period']);
});
```

Use it in Middleware (for example):
```php
class HttpRateLimitMiddleware implements MiddlewareInterface {

    /**
     * @var \RateLimiter\RateLimiter
     */
    private $rateLimiter;

    public function __construct(\RateLimiter\RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function __invoke(HttpRequest $request, HttpResponse $response, callable $next)
    {
        $ip = $request->getClientIp();

        if ($this->rateLimiter->check($ip)) {
            $response->setHeader('X-HTTP-RATELIMIT', $this->rateLimiter->getMaxRequests());
            $response->setHeader('X-HTTP-RATELIMIT-REMAINING', $this->rateLimiter->getAllow($ip));

            return $next($request, $response);
        }

        $response->setHeader('X-HTTP-RATELIMIT', $this->rateLimiter->getMaxRequests());
        $response->setHeader('X-HTTP-RATELIMIT-REMAINING', 0);
        $response->setStatusCode(429, 'Too many requests');
        $response->send();
        die();
    }
}
```

## Run tests:
* `vendor/bin/codecept run`