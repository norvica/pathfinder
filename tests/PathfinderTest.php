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

namespace Tests\Norvica\Pathfinder;

use Generator;
use Norvica\Pathfinder\Pathfinder;
use PHPUnit\Framework\TestCase;

final class PathfinderTest extends TestCase
{
    private Pathfinder $pathfinder;

    protected function setUp(): void
    {
        $this->pathfinder = new Pathfinder();
        $this->pathfinder->get('/users', '5b363eaae19cf963');
        $this->pathfinder->get('/users/{username}', 'c633d0a90d1c55f1');
        $this->pathfinder->get('/articles/{id:[0-9]+}[/{slug}]', 'f5143d8b6ebed877');
        $this->pathfinder->get('/orders/{uuid:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}', 'b2ae9b4a601718ca');
        $this->pathfinder->get('/items[/{id:[0-9]+}]', 'a912880dec221550');
        $this->pathfinder->get('/course/{p1:[0-9]{4}}/{p2:[a-z]+}/materials', '82caa8f5f9a3f9a8');
        $this->pathfinder->get('/course/{p1:[0-9]{4}}/{p2:[a-z]+}/summary', '12a9e4f30bfd853e');
        $this->pathfinder->get('/games/{id:\d+}', 'c5278e1c2b89a1d2');
        $this->pathfinder->put('/games/{id:\d+}', 'eb6e62b3907fb7c4');
        $this->pathfinder->get('/games/{slug:[\w-]+}', 'c1ee1cc6381d590c');
        $this->pathfinder->get('/games/reviews', '69d82a5755545ceb');
        $this->pathfinder->post('/games/reviews', 'a08cd78c88bea630');
    }

    public static function dataProvider(): Generator
    {
        yield 'static route' => ['GET', '/users', '5b363eaae19cf963', []];
        yield 'dynamic route with parameter' => ['GET', '/users/charlie', 'c633d0a90d1c55f1', ['username' => 'charlie']];
        yield 'dynamic route with required and optional parameter (missing)' => ['GET', '/articles/9999', 'f5143d8b6ebed877', ['id' => '9999']];
        yield 'dynamic route with required and optional parameter (passed)' => ['GET', '/articles/9999/how-to', 'f5143d8b6ebed877', ['id' => '9999', 'slug' => 'how-to']];
        yield 'dynamic route with UUID parameter' => ['GET', '/orders/97e39602-d36b-432f-8814-9287e89a4bf3', 'b2ae9b4a601718ca', ['uuid' => '97e39602-d36b-432f-8814-9287e89a4bf3']];
        yield 'dynamic route with optional parameter (missing)' => ['GET', '/items', 'a912880dec221550', []];
        yield 'dynamic route with optional parameter (passed)' => ['GET', '/items/9999', 'a912880dec221550', ['id' => '9999']];
        yield 'dynamic route with 2 consequent parameters' => ['GET', '/course/6666/abcd/materials', '82caa8f5f9a3f9a8', ['p1' => '6666', 'p2' => 'abcd']];
        yield 'dynamic route with 2 consequent parameters, but different ending' => ['GET', '/course/9999/dcba/summary', '12a9e4f30bfd853e', ['p1' => '9999', 'p2' => 'dcba']];
        yield 'dynamic route "GET games" with ID parameter' => ['GET', '/games/3333', 'c5278e1c2b89a1d2', ['id' => '3333']];
        yield 'dynamic route "PUT games" with ID parameter' => ['PUT', '/games/3333', 'eb6e62b3907fb7c4', ['id' => '3333']];
        yield 'dynamic route "GET games" with slug parameter' => ['GET', '/games/pac-man', 'c1ee1cc6381d590c', ['slug' => 'pac-man']];
        yield 'static route "GET games"' => ['GET', '/games/reviews', '69d82a5755545ceb', []];
        yield 'static route "POST games"' => ['POST', '/games/reviews', 'a08cd78c88bea630', []];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMatch(string $method, string $path, string $handler, array $params): void
    {
        $result = $this->pathfinder->match($method, $path);

        $this->assertEquals([$handler, $params], $result);
    }

    public static function notFoundProvider(): Generator
    {
        yield 'route that does not exist' => ['/does-not-exist'];
        yield 'route with parameter not matching regex' => ['/items/foo'];
    }

    /**
     * @dataProvider notFoundProvider
     */
    public function testNotFound(string $path): void
    {
        $this->assertEquals(404, $this->pathfinder->match('GET', $path)[0]);
    }

    public function testMethodNotAllowed(): void
    {
        $this->pathfinder->route('POST', '/bananas', '004b33884b396430');

        $this->assertEquals(405, $this->pathfinder->match('GET', '/bananas')[0]);
    }
}
