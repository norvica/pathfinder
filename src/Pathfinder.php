<?php

declare(strict_types=1);

namespace Norvica\Pathfinder;

/*
 * This file is part of norvica/pathfinder.
 *
 * (c) Siarhei Kvashnin <serge@norvica.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use UnexpectedValueException;

final class Pathfinder
{
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;

    private const CHILDREN = 0;
    private const HANDLERS = 1;
    private const PARAM = 2;
    private const REGEX = 3;

    private const METHODS = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'DELETE',
        'CONNECT',
        'OPTIONS',
        'TRACE',
        'PATCH',
        '*',
    ];

    private array $static;
    private array $dynamic;

    public function __construct(array $cached = null)
    {
        $this->static = $cached[0] ?? [];
        $this->dynamic = $cached[1] ?? self::dynamic();
    }

    public function get(string $pattern, string $handler): void
    {
        $this->route('GET', $pattern, $handler);
    }

    public function post(string $pattern, string $handler): void
    {
        $this->route('POST', $pattern, $handler);
    }

    public function put(string $pattern, string $handler): void
    {
        $this->route('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, string $handler): void
    {
        $this->route('DELETE', $pattern, $handler);
    }

    public function options(string $pattern, string $handler): void
    {
        $this->route('OPTIONS', $pattern, $handler);
    }

    public function patch(string $pattern, string $handler): void
    {
        $this->route('PATCH', $pattern, $handler);
    }

    public function any(string $pattern, string $handler): void
    {
        $this->route('*', $pattern, $handler);
    }

    public function route(string $method, string $pattern, string $handler): void
    {
        $method = strtoupper($method);
        if (!in_array($method, self::METHODS)) {
            throw new UnexpectedValueException("Unknown method '{$method}' given.");
        }

        if (!str_contains($pattern, '{')) {
            if (!isset($this->static[$pattern])) {
                $this->static[$pattern] = self::static();
            }

            $this->static[$pattern][self::HANDLERS][$method] = $handler;

            return;
        }

        $node = &$this->dynamic;
        // tokenize
        preg_match_all('/\{(\w+):?([^\/]*)}|\[|]|[^\/\[\]{}]+/', trim($pattern, '/'), $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $token = $match[0];

            // leaf
            if ('[' === $token) {
                $node[self::HANDLERS][$method] = $handler;

                continue;
            }

            // optional part is the latest
            if ($token === ']') {
                break;
            }

            if (isset($match[1])) {
                // dynamic
                if (!isset($node[self::CHILDREN][$token])) {
                    $child = self::dynamic();
                    $child[self::PARAM] = $match[1];
                    $child[self::REGEX] = !empty($match[2]) ? $match[2] : null;
                    $node[self::CHILDREN][$token] = $child;
                }
            } elseif (!isset($node[self::CHILDREN][$token])) {
                // static
                $node[self::CHILDREN][$token] = self::static();
            }

            // move
            $node = &$node[self::CHILDREN][$token];
        }

        $node[self::HANDLERS][$method] = $handler;
    }

    public function match(string $method, string $path): array
    {
        $method = strtoupper($method);
        if (isset($this->static[$path])) {
            if (!isset($this->static[$path][self::HANDLERS][$method])) {
                // any
                if (isset($this->static[$path][self::HANDLERS]['*'])) {
                    return [$this->static[$path][self::HANDLERS]['*'], []];
                }

                return [self::METHOD_NOT_ALLOWED, []];
            }

            return [$this->static[$path][self::HANDLERS][$method], []];
        }

        $params = [];
        $node = $this->dynamic;
        // tokenize
        $tokens = explode('/', trim($path, '/'));
        foreach ($tokens as $token) {
            // static match
            if (isset($node[self::CHILDREN][$token])) {
                $node = $node[self::CHILDREN][$token];

                continue;
            }

            // dynamic matching
            $found = false;
            foreach ($node[self::CHILDREN] as $child) {
                // static mismatches (optimize?)
                if (!isset($child[self::PARAM])) {
                    continue;
                }

                if (null === $child[self::REGEX]) {
                    $params[$child[self::PARAM]] = $token;
                    $node = $child;
                    $found = true;

                    break;
                }

                if (!preg_match("/^{$child[self::REGEX]}\$/", $token)) {
                    continue;
                }

                $params[$child[self::PARAM]] = $token;
                $node = $child;
                $found = true;

                break;
            }

            if (!$found) {
                return [self::NOT_FOUND, []];
            }
        }

        if ($node[self::HANDLERS]) {
            if (!isset($node[self::HANDLERS][$method])) {
                // any
                if (isset($node[self::HANDLERS]['*'])) {
                    return [
                        $node[self::HANDLERS]['*'],
                        $params,
                    ];
                }

                return [self::METHOD_NOT_ALLOWED, []];
            }

            return [
                $node[self::HANDLERS][$method],
                $params,
            ];
        }

        return [self::NOT_FOUND, []];
    }

    public function tree(): array
    {
        return [
            $this->static,
            $this->dynamic,
        ];
    }

    private static function static(): array
    {
        return [
            self::CHILDREN => [],
            self::HANDLERS => [],
        ];
    }

    private static function dynamic(): array
    {
        return [
            self::CHILDREN => [],
            self::HANDLERS => [],
            self::PARAM => null,
            self::REGEX => null,
        ];
    }
}
