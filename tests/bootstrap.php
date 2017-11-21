<?php

require __DIR__ . '/../vendor/autoload.php';

class DummyRoutesCollection extends \Isholao\Router\RouteCollection
{

    public $routes = [];

    public function addRoute(\Isholao\Router\RouteInterface $route): \Isholao\Router\RouteInterface
    {
        $route = parent::addRoute($route);
        $this->routes[] = [$route->getHttpMethod(), $route->getPath(), $route->getResponder()];
        return $route;
    }

}
