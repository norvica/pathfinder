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

final class Cache
{
    public function __construct(
        private readonly string $filename,
    ) {
    }

    public function write(array $tree): void
    {
        $cache = serialize($tree);
        file_put_contents($this->filename, $cache);
    }

    public function read(): array
    {
        $cache = file_get_contents($this->filename);

        return unserialize($cache, ['allowed_classes' => true]);
    }
}
