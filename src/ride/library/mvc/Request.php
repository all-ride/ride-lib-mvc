<?php

namespace ride\library\mvc;

use ride\library\mvc\exception\MvcException;
use ride\library\http\Request as HttpRequest;
use ride\library\router\Route;

/**
 * A extension of the HTTP request with route and URL functionality
 */
class Request extends HttpRequest {

    /**
     * Route of this request
     * @var \ride\library\router\Route
     */
    protected $route;

    /**
     * Base URL of the request to path
     * @var string
     */
    protected $baseUrl;

    /**
     * Base URL of the request to the main PHP script
     * @var string
     */
    protected $baseScript;

    /**
     * Request path on the base URL
     * @var string
     */
    protected $basePath;

    /**
     * Sets the selected route
     * @param \ride\library\router\Route $route
     * @return null
     */
    public function setRoute(Route $route) {
        $this->route = $route;
    }

    /**
     * Gets the selected route
     * @return \ride\library\router\Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Gets the URL instance from the route
     * @return \ride\library\router\Url
     */
    public function getRouteUrl() {
        if (!$this->route) {
            throw new MvcException('Could not get the URL from the route: no route set');
        }

        $arguments = $this->route->getPredefinedArguments() + $this->route->getArguments();

        return $this->route->getUrl($this->getBaseScript(), $arguments, $this->getQueryParameters());
    }

    /**
     * Sets the requested path and detects base URL, script and path
     * @param string $path The requested path
     * @throws \ride\library\http\exception\HttpException when an invalid path
     * is provided
     */
    protected function setPath($path) {
        parent::setPath($path);

        $baseUrl = '/';
        $baseScript = null;

        if (isset($_SERVER['SCRIPT_NAME']) && !isset($_SERVER['SHELL'])) {
            $position = strrpos($_SERVER['SCRIPT_NAME'], '/');
            if ($position !== false) {
                if (strpos($path, $_SERVER['SCRIPT_NAME']) === 0) {
                    // script is literally accessed without rewrite
                    $baseUrl = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
                    $baseScript = $_SERVER['SCRIPT_NAME'];
                } else {
                    // path is rewritten, detect script
                    $positionIndex = strpos($_SERVER['SCRIPT_NAME'], '/index.php');
                    if ($positionIndex !== 0) {
                        $baseUrl = substr($_SERVER['SCRIPT_NAME'], 0, $positionIndex);
                    } else {
                        $baseUrl = substr($_SERVER['SCRIPT_NAME'], 0, $position);
                    }

                    $baseScript = $baseUrl;
                }
            }
        } elseif (!isset($_SERVER['SCRIPT_NAME'])) {
            $positionPhp = strpos($path, 'index.php');
            if ($positionPhp !== false) {
                // a php script in the request path
                $positionParent = strrpos(substr($path, 0, $positionPhp), '/');
                if ($positionParent !== false) {
                    $baseUrl = substr($path, 0, $positionParent);
                }

                $baseScript = substr($path, 0, $positionPhp + 9);
            }
        }

        $server = $this->getServerUrl();

        $this->baseUrl = rtrim($server . $baseUrl, '/');
        $this->baseScript = rtrim($server . $baseScript, '/');
        $this->basePath = rtrim(str_replace($this->baseUrl, '', str_replace($this->baseScript, '', $server . $path)), '/');
        if (!$this->basePath) {
            $this->basePath = '/';
        }
    }

    /**
     * Gets the path on the running script
     * @return string
     */
    public function getBasePath($removeQueryString = false) {
        if ($removeQueryString) {
            $positionQuestionMark = strpos($this->basePath, '?');
            if ($positionQuestionMark) {
                return substr($this->basePath, 0, $positionQuestionMark);
            }
        }

        return $this->basePath;
    }

    /**
     * Override the base script of the request
     * @param string $baseScript
     * @return null
     */
    public function setBaseScript($baseScipt) {
        $this->baseScript = $baseScipt;
    }

    /**
     * Gets the base URL to the running script
     * @return string
     */
    public function getBaseScript() {
        return $this->baseScript;
    }

    /**
     * Gets the base URL to the application
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

}
