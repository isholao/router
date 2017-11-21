<?php

namespace Isholao\Router;

/**
 * @author Ishola O <ishola.tolu@outlook.com>
 */
interface RouteCollectionInterface
{

    public function dispatch(string $httpMethod, string $uri): ?RouteInterface;
    
    public function hasRoute(string $httpMethod, string $uri): bool;

    public static function addSegmentRegex(string $name, string $regex);

    public function getPathFor(string $name, array $params = [],
                               ?string $default = NULL): ?string;

    public function groupRoutes(string $groupPrefix, callable $callable);

    public function patch(string $path, $responder, ?string $name = NULL,
                          array $data = []): IRoute;

    public function get(string $path, $responder, ?string $name = NULL,
                        array $data = []): IRoute;

    public function post(string $path, $responder, ?string $name = NULL,
                         array $data = []): IRoute;

    public function delete(string $path, $responder, ?string $name = NULL,
                           array $data = []): IRoute;

    public function options(string $path, $responder, ?string $name = NULL,
                           array $data = []): IRoute;

    public function head(string $path, $responder, ?string $name = NULL,
                         array $data = []): IRoute;

    public function put(string $path, $responder, ?string $name = NULL,
                        array $data = []): IRoute;

    public function mapOne(string $httpMethod, string $path, $responder,
                           ?string $name = NULL, array $data = []): IRoute;

    public function mapMany(string $httpMethods, string $path, $responder,
                            array $data = []);
    
}
