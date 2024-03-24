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

use Norvica\Pathfinder\Contract\RouteInterface;

class Route implements RouteInterface
{
    private string $id;
    private $handler;
    private array $parameters;

    public function __construct(
        string $id,
        callable|array|string $handler,
        array $parameters,
    ) {
        $this->id = $id;
        $this->handler = $handler;
        $this->parameters = $parameters;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function handler(): callable|array|string
    {
        return $this->handler;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
