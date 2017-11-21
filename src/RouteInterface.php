<?php

namespace Isholao\Router;

/**
 * @author Ishola O <ishola.tolu@outlook.com>
 */
interface RouteInterface
{
    
    public function setResponder($responder);

    public function getResponder();

    public function setName(string $name);

    public function getName(): ?string;

    public function containsRegex(bool $hasRegex);

    public function hasRegex(): bool;

    public function setHttpMethod(string $method);

    public function getHttpMethod(): string;

    public function setPath(string $path);

    public function getPath(): string;

    public function setOptions(array $options);

    public function getOptions(): array;

    public function setPathSegmentParams(array $params);

    public function getPathSegmentParams(): array;

    public function addOption(string $key, $value);

    public function getOption(string $key, $default = NULL);

    public function setPathSegmentParam(string $key, $value);

    public function getPathSegmentParam(string $key, $default = NULL);
}
