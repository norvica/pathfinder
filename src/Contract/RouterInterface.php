<?php

declare(strict_types=1);

namespace Norvica\Pathfinder\Contract;

interface RouterInterface
{
    public function get(string $pattern, callable|array|string $handler): static;

    public function post(string $pattern, callable|array|string $handler): static;

    public function patch(string $pattern, callable|array|string $handler): static;

    public function put(string $pattern, callable|array|string $handler): static;

    public function delete(string $pattern, callable|array|string $handler): static;

    public function options(string $pattern, callable|array|string $handler): static;

    public function any(string $pattern, callable|array|string $handler): static;

    public function route(array|string $methods, string $pattern, callable|array|string $handler): static;
}
