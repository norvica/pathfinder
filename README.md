# Pathfinder

[![Latest Stable Version](https://poser.pugx.org/norvica/pathfinder/v/stable.png)](https://packagist.org/packages/norvica/pathfinder)
[![Checks](https://github.com/norvica/pathfinder/actions/workflows/checks.yml/badge.svg)](https://github.com/norvica/pathfinder/actions/workflows/checks.yml)

**Pathfinder** is a lean and extremely fast PHP router built on the [Trie](https://en.wikipedia.org/wiki/Trie) (or
prefix tree) search algorithm. Its design enables quick lookups and is optimized for high performance.

## Install

```bash
composer require norvica/pathfinder
```

## Use

If you'd like to use the lower-level `Pathfinder` class directly, please refer to the 
[appropriate documentation section](./doc/pathfinder.md#use). The `Router` class provides a convenient wrapper and performs
matching against PSR-7 `ServerRequestInterface` objects.

If you'd like to use a more low level class `Pathfinder` please refer to an 
[appropriate documentation section](./doc/pathfinder.md#use). `Router` class provides a convenience wrapper and is matching
against PSR-7 `ServerRequestInterface`.

To get started, create a `Router` instance, define your routes, and then match your incoming request:

```php
use Norvica\Pathfinder\Router;

// Create a Pathfinder instance
$router = new Router();

// Define your routes using closures
$definitions = static function(Router $router) {
    $router->route('POST', '/orders', OrderPostController::class);
    $router->route('GET', '/orders/{id}', [OrderGetController::class, '__invoke']);
};

// Load your route definitions
$definitions($router);

// NOTICE: This is an example of request instantiation.
// Use the library of your choice that instantiates PSR-7 request
// (e.g. https://github.com/guzzle/psr7).
$request = ServerRequest::fromGlobals();

// Handling a request
try {
    $route = $router->match($request);
    $handler = $route->handler();
    $parameters  = $route->parameters();
    // [...] execute matched handler
} catch (\Norvica\Pathfinder\Exception\NotFound $e) {
    // [...] handle 404
} catch (\Norvica\Pathfinder\Exception\MethodNotAllowed $e) {
    // [...] handle 405
}
```

## Routes Definition

### Basic Route

A basic route is a simple, static path:

```php
$router->route('GET', '/orders', OrderListController::class);
```

### Parameterized Route

To include parameters like a username or ID in the URL:

```php
$router->route('GET', '/orders/{id}', OrderGetController::class);
```

### Regular Expression Constraints

You can add regex constraints to your parameters:

```php
$router->route('GET', '/orders/{id:[0-9]+}', OrderGetController::class);
```

### Optional Parameters

You can define routes with optional segments:

```php
$router->route('GET', '/articles/{id:[0-9]+}[/{slug}]', ArticleGetController::class);
```

### Complex Routes

You can also define more complex routes with multiple parameters:

```php
$router->route('GET', '/course/{year:[0-9]{4}}/{subject:[a-z]+}/{code:[0-9a-f]{4}}', CourseGetController::class);
```

### Enumerated Parameters

For when you have a specific set of acceptable parameter values:

```php
$router->route('GET', '/{language:en|de}/profile', ProfileGetController::class);
```

### Shortcuts

For convenience, `Router` offers shortcut methods for common HTTP request methods.

```php
$router->get('/orders', 'get_orders_handler');
$router->post('/orders', 'post_orders_handler');
$router->put('/orders/{id}', 'put_order_handler');
$router->patch('/orders/{id}', 'patch_order_handler');
$router->delete('/orders/{id}', 'delete_order_handler');
// ...
```

You can use these shortcut methods just like you would use the route method, but without the need to specify the HTTP
method as the first argument.

> [!IMPORTANT]
> The router only facilitates matching `HEAD` requests to `GET` routes when a specific `HEAD` handler is not found.
> Developers must explicitly ensure that `HEAD` method calls always return 
> [empty response bodies](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD).

## Tests

Run the PHPUnit tests to ensure that the package is functioning as expected:

```bash
./vendor/bin/phpunit --testdox
```

## References

- [Wikipedia article about Trie](https://en.wikipedia.org/wiki/Trie)
- [HackerRank video about Trie](https://www.youtube.com/watch?v=zIjfhVPRZCg)
