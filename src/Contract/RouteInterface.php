<?php

declare(strict_types=1);

namespace Norvica\Pathfinder\Contract;

interface RouteInterface
{
    public function id(): string;

    public function handler(): callable|array|string;

    public function parameters(): array;
}
