<?php

declare(strict_types=1);

use Norvica\Pathfinder\Cache;
use Norvica\Pathfinder\Pathfinder;

require __DIR__ . '/../src/Cache.php';
require __DIR__ . '/../src/Pathfinder.php';

$pathfinder = setup();
for($i = 0; $i < 10000; ++$i) {
    $pathfinder->match('POST', '/api/workflow/9999/stages/6666/tasks/3333/subtasks/1111/complete');
}

function setup(): Pathfinder
{
    $pathfinder = new Pathfinder();
    $routing = require __DIR__ . '/routes/routes.php';
    $routing($pathfinder);

    $tree = $pathfinder->tree();
    (new Cache(__DIR__ . '/../var/pathfinder_cache'))->write($tree);

    $tree = (new Cache(__DIR__ . '/../var/pathfinder_cache'))->read();
    return new Pathfinder($tree);
}
