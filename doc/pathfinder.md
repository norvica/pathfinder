# Utilizing Pathfinder Class

This part of the documentation describes the usage of the `Pathfinder` class. If you'd prefer to use the convenient `Router`
wrapper, please refer to the [appropriate documentation section](../README.md#use).

## Use

To get started, create a `Pathfinder` instance, define your routes, and then match your incoming request:

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

For convenience, `Pathfinder` offers shortcut methods for common HTTP request methods.

```php
$pathfinder->get('/orders', 'get_orders_handler');
$pathfinder->post('/orders', 'post_orders_handler');
$pathfinder->put('/orders/{id}', 'put_order_handler');
$pathfinder->patch('/orders/{id}', 'patch_order_handler');
$pathfinder->delete('/orders/{id}', 'delete_order_handler');
```

You can use these shortcut methods just like you would use the route method, but without the need to specify the HTTP
method as the first argument.
