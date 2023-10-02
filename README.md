# Pathfinder

[![Latest Stable Version](https://poser.pugx.org/norvica/pathfinder/v/stable.png)](https://packagist.org/packages/norvica/pathfinder)
[![Checks](https://github.com/norvica/pathfinder/actions/workflows/checks.yml/badge.svg)](https://github.com/norvica/pathfinder/actions/workflows/checks.yml)

**Pathfinder** is a lean and extremely fast PHP router built on the [Trie](https://en.wikipedia.org/wiki/Trie) (or
prefix tree) search algorithm. Its design enables quick lookups and is optimized for high performance.

## Install

```bash
composer require norvica/pathfinder
```

Requires PHP `^8.1`.

## Use

To get started, import the library and create a Pathfinder instance:

```php
use Norvica\Pathfinder\Pathfinder;

// Create a Pathfinder instance
$pathfinder = new Pathfinder();

// Define your routes using closures
$definitions = static function(Pathfinder $pathfinder) {
    $pathfinder->route('GET', '/orders', 'get_orders_handler');
    $pathfinder->route('POST', '/orders', 'post_order_handler');
    $pathfinder->route('GET', '/orders/{id}', 'get_order_handler');
};

// Load your route definitions
$definitions($pathfinder);

// Determine the request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Split URI from query string
[$uri] = explode('?', $uri);

// Decode URI
$uri = rawurldecode($uri);

[$handler, $parameters] = $pathfinder->match($method, $uri);
match ($handler) {
    404 => /* handle 404 */,
    405 => /* handle 405 */,
};
```

## Routes Definition

### Basic Route

A basic route is a simple, static path:

```php
// Define
$pathfinder->route('GET', '/orders', 'get_orders_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/orders');
// ['get_orders_handler', []]
```

### Parameterized Route

To include parameters like a username or ID in the URL:

```php
// Define
$pathfinder->route('GET', '/orders/{id}', 'get_order_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/orders/9999');
// ['get_order_handler', ['id' => '9999']]

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/orders/abcd');
// ['get_order_handler', ['id' => 'abcd']]
```

### Regular Expression Constraints

You can add regex constraints to your parameters:

```php
// Define
$pathfinder->route('GET', '/orders/{id:[0-9]+}', 'get_order_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/orders/9999');
// ['get_order_handler', ['id' => '9999']]

// Mismatch
[$handler, $parameters] = $pathfinder->match('GET', '/orders/abcd');
// [404, []]
```

### Optional Parameters

You can define routes with optional segments:

```php
// Define
$pathfinder->route('GET', '/articles/{id:[0-9]+}[/{slug}]', 'get_article_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/articles/9999/my-article');
// ['get_article_handler', ['id' => '9999', 'slug' => 'my-article']]

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/articles/9999');
// ['get_article_handler', ['id' => '9999']]
```

### Complex Routes

You can also define more complex routes with multiple parameters:

```php
// Define
$pathfinder->route('GET', '/course/{year:[0-9]{4}}/{subject:[a-z]+}/{code:[0-9a-f]{4}}', 'get_course_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/course/2023/math/34cd');
// ['get_course_handler', ['year' => '2023', 'subject' => 'math', 'code' => '34cd']]
```

### Enumerated Parameters

For when you have a specific set of acceptable parameter values:

```php
// Define
$pathfinder->route('GET', '/{language:en|de}/profile', 'get_profile_handler');

// Match
[$handler, $parameters] = $pathfinder->match('GET', '/en/profile');
// ['get_profile_handler', ['language' => 'en']]
```

### Shortcuts

For convenience, **Pathfinder** offers shortcut methods for common HTTP request methods.

```php
$pathfinder->get('/orders', 'get_orders_handler');
$pathfinder->post('/orders', 'post_orders_handler');
$pathfinder->put('/orders/{id}', 'put_order_handler');
$pathfinder->patch('/orders/{id}', 'patch_order_handler');
$pathfinder->delete('/orders/{id}', 'delete_order_handler');
```

You can use these shortcut methods just like you would use the route method, but without the need to specify the HTTP
method as the first argument.

## Tests

Run the PHPUnit tests to ensure that the package is functioning as expected:

```bash
./vendor/bin/phpunit --testdox
```

## References

- [Wikipedia article about Trie](https://en.wikipedia.org/wiki/Trie)
- [HackerRank video about Trie](https://www.youtube.com/watch?v=zIjfhVPRZCg)
