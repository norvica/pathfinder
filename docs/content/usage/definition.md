---
title: "Routes Definition"
description: ""
date: 2024-05-06T19:33:08+02:00
lastmod: 2024-05-06T19:33:08+02:00
draft: false
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

## Basic Route

A basic route is a simple, static path:

```php
$router->route('GET', '/orders', OrderListController::class);
```

## Parameterized Route

To include parameters like a username or ID in the URL:

```php
$router->route('GET', '/orders/{id}', OrderGetController::class);
```

## Regular Expression Constraints

You can add regex constraints to your parameters:

```php
$router->route('GET', '/orders/{id:[0-9]+}', OrderGetController::class);
```

## Optional Parameters

You can define routes with optional segments:

```php
$router->route('GET', '/articles/{id:[0-9]+}[/{slug}]', ArticleGetController::class);
```

## Complex Routes

You can also define more complex routes with multiple parameters:

```php
$router->route('GET', '/course/{year:[0-9]{4}}/{subject:[a-z]+}/{code:[0-9a-f]{4}}', CourseGetController::class);
```

## Enumerated Parameters

For when you have a specific set of acceptable parameter values:

```php
$router->route('GET', '/{language:en|de}/profile', ProfileGetController::class);
```

## Shortcuts

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

{{< callout context="note" icon="alert-triangle" >}}
The router only facilitates matching `HEAD` requests to `GET` routes when a specific `HEAD` handler is not found.
Developers must explicitly ensure that `HEAD` method calls always return [empty response bodies](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD).
{{< /callout >}}
