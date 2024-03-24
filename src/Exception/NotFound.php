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

namespace Norvica\Pathfinder\Exception;

use RuntimeException;
use Throwable;

final class NotFound extends RuntimeException
{
    public function __construct(string $message = 'Not Found', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
