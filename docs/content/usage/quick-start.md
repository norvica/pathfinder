---
title: "Quick Start"
description: ""
summary: ""
date: 2024-05-06T20:45:42+02:00
lastmod: 2024-05-06T20:45:42+02:00
draft: false
weight: 110
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

**Pathfinder** is a lean and fast PHP router built on the [Trie](https://en.wikipedia.org/wiki/Trie) (or
prefix tree) search algorithm. Its design enables quick lookups and is optimized for high performance.

Requires **PHP 8.1+**.

## Install

This library is installed using Composer. If you don't have Composer, you can get it from
[getcomposer.org](https://getcomposer.org).

In your project's root directory, run the following command:

```bash
composer require norvica/pathfinder
```

## Use

{{< callout context="note" icon="info-circle" >}}
If you'd like to use the lower-level `Pathfinder` class directly, please refer to the
[appropriate documentation section](/usage/pathfinder). The `Router` class provides a convenient
wrapper and performs matching against PSR-7 `ServerRequestInterface` objects.
{{< /callout >}}

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
