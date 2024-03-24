<?php

declare(strict_types=1);

namespace Tests\Norvica\Pathfinder;

use Generator;
use Norvica\Pathfinder\Exception\MethodNotAllowed;
use Norvica\Pathfinder\Exception\NotFound;
use Norvica\Pathfinder\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->get('/users', '5b363eaae19cf963');
        $this->router->any('/', '1c97b75828e480bd');
    }

    public static function dataProvider(): Generator
    {
        yield 'static route' => ['GET', '/users', '5b363eaae19cf963', []];
        yield 'static route with "HEAD"' => ['HEAD', '/users', '5b363eaae19cf963', []];
        yield 'static route with wildcard method with "OPTIONS"' => ['OPTIONS', '/', '1c97b75828e480bd', []];
        yield 'static route with wildcard method with "GET"' => ['GET', '/', '1c97b75828e480bd', []];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMatch(string $method, string $path, string $handler, array $params): void
    {
        $request = $this->request($method, $path);

        $route = $this->router->match($request);

        $this->assertEquals($handler, $route->handler());
        $this->assertEquals($params, $route->parameters());
    }

    public function testNotFound(): void
    {
        $this->expectException(NotFound::class);
        $request = $this->request('PATCH', '/some/non-existing/path');

        $this->router->match($request);
    }

    public function testMethodNotAllowed(): void
    {
        $this->expectException(MethodNotAllowed::class);
        $request = $this->request('PATCH', '/users');

        $this->router->match($request);
    }

    private function request(string $method, string $path): ServerRequestInterface
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getPath')->willReturn($path);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);

        return $request;
    }
}
