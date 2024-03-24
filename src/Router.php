<?php

declare(strict_types=1);

/*
 * This file is part of norvica/pathfinder.
 *
 * (c) Siarhei Kvashnin <serge@norvica.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Norvica\Pathfinder;

use Norvica\Pathfinder\Contract\RouterInterface;
use Norvica\Pathfinder\Exception\MethodNotAllowed;
use Norvica\Pathfinder\Exception\NotFound;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    private array $handlers;

    private Pathfinder $pathfinder;

    public function __construct(Pathfinder $pathfinder = null)
    {
        $this->handlers = [];
        $this->pathfinder = $pathfinder ?? new Pathfinder();
    }

    public function get(string $pattern, callable|array|string $handler): static
    {
        $this->route('GET', $pattern, $handler);

        return $this;
    }

    public function post(string $pattern, callable|array|string $handler): static
    {
        $this->route('POST', $pattern, $handler);

        return $this;
    }

    public function patch(string $pattern, callable|array|string $handler): static
    {
        $this->route('PATCH', $pattern, $handler);

        return $this;
    }

    public function put(string $pattern, callable|array|string $handler): static
    {
        $this->route('PUT', $pattern, $handler);

        return $this;
    }

    public function delete(string $pattern, callable|array|string $handler): static
    {
        $this->route('DELETE', $pattern, $handler);

        return $this;
    }

    public function options(string $pattern, callable|array|string $handler): static
    {
        $this->route('OPTIONS', $pattern, $handler);

        return $this;
    }

    public function any(string $pattern, callable|array|string $handler): static
    {
        $this->route('*', $pattern, $handler);

        return $this;
    }

    public function route(array|string $methods, string $pattern, callable|array|string $handler): static
    {
        $methods = is_string($methods) ? [$methods] : $methods;
        $id = bin2hex(random_bytes(8));
        $this->handlers[$id] = $handler;
        foreach ($methods as $method) {
            $this->pathfinder->route($method, $pattern, $id);
        }

        return $this;
    }

    public function match(ServerRequestInterface $request): Route
    {
        $method = strtoupper($request->getMethod());
        if ('HEAD' === $method) {
            $method = 'GET';
        }

        [$id, $parameters] = $this->pathfinder->match($method, $request->getUri()->getPath());

        return match ($id) {
            404 => throw new NotFound(),
            405 => throw new MethodNotAllowed(),
            default => new Route(
                $id,
                $this->handlers[$id],
                $parameters,
            ),
        };
    }
}
