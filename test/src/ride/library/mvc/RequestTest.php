<?php

namespace ride\library\mvc;

use ride\library\http\HeaderContainer;
use ride\library\router\Route;
use ride\library\router\Url;

use \PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase {

    public function testRoute() {
        $request = new Request('/path');

        $this->assertNull($request->getRoute());

        $route = new Route('/path', 'callback');
        $request->setRoute($route);

        $this->assertEquals($route, $request->getRoute());
    }

    public function testGetRouteUrl() {
        $route = new Route('/path/to/%action%', 'callback');
        $route->setArguments(array('action' => 'edit'));

        $headers = new HeaderContainer();
        $headers->setHeader('host', 'www.host.com');

        $request = new Request('/path/to/edit', 'GET', 'HTTP/1.1', $headers);
        $request->setRoute($route);

        $url = $request->getRouteUrl();

        $this->assertTrue($url instanceof Url);
        $this->assertEquals('http://www.host.com/path/to/edit', (string) $url);
        $this->assertEquals('edit', $url->getArgument('action'));
    }

    public function testPath() {
        if (isset($_SERVER['SHELL'])) {
            unset($_SERVER['SHELL']);
        }
        if (isset($_SERVER['SCRIPT_NAME'])) {
            unset($_SERVER['SCRIPT_NAME']);
        }

        $request = new Request('/path/index.php/foo/bar');

        $this->assertEquals('/foo/bar', $request->getBasePath());
        $this->assertEquals('http://localhost/path/index.php', $request->getBaseScript());
        $this->assertEquals('http://localhost/path', $request->getBaseUrl());

        $request = new Request('/path/index.php');

        $this->assertEquals('/', $request->getBasePath());
        $this->assertEquals('http://localhost/path/index.php', $request->getBaseScript());
        $this->assertEquals('http://localhost/path', $request->getBaseUrl());

        $_SERVER['SCRIPT_NAME'] = '/path/index.php';
        $request = new Request('/path/foo/bar');

        $this->assertEquals('/foo/bar', $request->getBasePath());
        $this->assertEquals('http://localhost/path', $request->getBaseScript());
        $this->assertEquals('http://localhost/path', $request->getBaseUrl());

        $_SERVER['SCRIPT_NAME'] = 'console.php';
        $_SERVER['SHELL'] = 'shell';
        $request = new Request('/path/foo/bar?test=value');

        $this->assertEquals('/path/foo/bar?test=value', $request->getBasePath());
        $this->assertEquals('/path/foo/bar', $request->getBasePath(true));
        $this->assertEquals('http://localhost', $request->getBaseScript());
        $this->assertEquals('http://localhost', $request->getBaseUrl());

        unset($_SERVER['SCRIPT_NAME']);
        $request = new Request('/path/foo/bar');

        $this->assertEquals('/path/foo/bar', $request->getBasePath());
        $this->assertEquals('http://localhost', $request->getBaseScript());
        $this->assertEquals('http://localhost', $request->getBaseUrl());
    }

}
