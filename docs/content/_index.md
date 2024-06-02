---
title : "Pathfinder"
description: ""
lead: "Lean and fast PHP router"
cta: "/usage/quick-start/"
date: 2024-05-06T19:33:08+02:00
lastmod: 2024-05-06T19:33:08+02:00
draft: false
seo:
 title: "Lean and fast PHP router" # custom title (optional)
 description: "" # custom description (recommended)
 canonical: "" # custom canonical URL (optional)
 noindex: false # false (default) or true
---

## Define and Match Routes

Provides a flexible way to define and match routes in your PHP projects.

```php
$router = new Router();

$router->route('POST', '/orders', [OrderController::class, 'post']);
$router->route('GET', '/orders/{id}', [OrderController::class, 'get']);

$request = ServerRequest::fromGlobals();

$route = $router->match($request);
```

[Learn more →](/usage/quick-start/)
