<?php

declare(strict_types=1);

namespace Tests\Norvica\Pathfinder\Bench;

use Norvica\Pathfinder\Pathfinder;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

#[Revs(100)]
#[Iterations(5)]
#[Warmup(2)]
#[OutputTimeUnit("milliseconds", 3)]
final class ColdBench
{
    public function benchFirstRoute(): void
    {
        $router = new Pathfinder();
        $routing = require __DIR__ . '/../routes/routes.php';
        $routing($router);
        $router->match('GET', '/users');
    }

    public function benchLastRoute(): void
    {
        $router = new Pathfinder();
        $routing = require __DIR__ . '/../routes/routes.php';
        $routing($router);
        $router->match('POST', '/api/workflow/9999/stages/6666/tasks/3333/subtasks/1111/complete');
    }

    public function bench404(): void
    {
        $router = new Pathfinder();
        $routing = require __DIR__ . '/../routes/routes.php';
        $routing($router);
        $router->match('GET', '/api/workflow/9999/stages/6666/tasks/3333/subtasks/1111/complete!');
    }
}
