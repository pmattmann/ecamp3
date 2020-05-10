<?php

declare(strict_types=1);

namespace eCamp\Lib\Router;

use Laminas\Stdlib\ArrayUtils;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class TemplatedFastRouteRouter implements RouterInterface {

    private $fastRouteRouter;
    private $fastRouteRouterReflector;

    public function __construct(FastRouteRouter $fastRouteRouter) {
        $this->fastRouteRouter = $fastRouteRouter;
    }

    public function addRoute(Route $route): void {
        $this->fastRouteRouter->addRoute($route);
    }

    public function match(Request $request): RouteResult {
        return $this->fastRouteRouter->match($request);
    }

    public function generateUri(string $name, array $substitutions = [], array $options = []): string {
        // Inject any pending routes
        $this->injectRoutes();

        $routes = $this->getRoutes();

        if (! array_key_exists($name, $routes)) {
            throw new \Mezzio\Router\Exception\RuntimeException(sprintf(
                'Cannot generate URI for route "%s"; route not found',
                $name
            ));
        }

        $route   = $routes[$name];
        $options = ArrayUtils::merge($route->getOptions(), $options);

        if (! empty($options['defaults'])) {
            $substitutions = array_merge($options['defaults'], $substitutions);
        }

        $routeParser = new TemplatedRouteParser();
        $route       = $routeParser->parse($route->getPath());

        // Generate the path
        $path = '';
        foreach ($route as $parts) {
            if (count($parts) == 1) {
                // Append the string
                $path .= $parts[0];
                continue;
            }

            $operation = array_shift($parts);
            $valParts = [];
            $tplParts = [];
            foreach ($parts as $part) {
                if (array_key_exists($part[0], $substitutions)) {
                    // Check substitute value with regex
                    if (!preg_match('~^' . $part[1] . '$~', (string)$substitutions[$part[0]])) {
                        throw new \Mezzio\Router\Exception\RuntimeException(sprintf(
                            'Parameter value for [%s] did not match the regex `%s`',
                            $part[0],
                            $part[1]
                        ));
                    }
                    $valParts[] = $part[0] . '=' . $substitutions[$part[0]];
                } else {
                    $tplParts[] = $part[0];
                }
            }
            while (count($valParts) > 0) {
                if ($operation == '?') {
                    $path .= $operation . array_shift($valParts);
                    $operation = '&';
                } else {
                    $path .= $operation . array_shift($valParts);
                }
            }
            if (count($tplParts) > 0) {
                $path .= '{' . $operation . implode(',', $tplParts) . '}';
            }
        }

        // Return generated path
        return $path;
    }


    private function getRoutes(): array {
        return $this->accessPrivateProperty('routes');
    }

    private function injectRoutes() {
        $this->invokePrivateMethod(__FUNCTION__);
    }


    private function getFastRouteRouterReflector(): \ReflectionObject {
        if ($this->fastRouteRouterReflector == null) {
            $this->fastRouteRouterReflector = new \ReflectionObject($this->fastRouteRouter);
        }
        return $this->fastRouteRouterReflector;
    }

    private function invokePrivateMethod($name, $arguments = []) {
        $method = $this->getFastRouteRouterReflector()->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->fastRouteRouter, $arguments);
    }

    private function accessPrivateProperty($name) {
        $property = $this->getFastRouteRouterReflector()->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($this->fastRouteRouter);
    }

}
