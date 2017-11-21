<?php

namespace Isholao\Router;

/**
 * @author Ishola O <ishola.tolu@outlook.com>
 */
class Route implements RouteInterface
{

    protected $path;
    protected $processedTemplate;
    protected $params = [];
    protected $httpMethod;
    protected $options = [];
    protected $hasRegex = FALSE;
    protected $name = NULL;
    protected $responder;

    /* Set route responder
     *
     * @param string|object|callable $responder
     * @return \Router\Route
     */

    public function setResponder($responder)
    {
        $this->responder = $responder;
        return $this;
    }

    /**
     * Get route responder
     * 
     * @return mixed
     */
    public function getResponder()
    {
        return $this->responder;
    }

    /**
     * Set name for the route
     * 
     * @param string $name
     * @return \Router\Route
     */
    public function setName(string $name)
    {
        if (empty($name))
        {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        $this->name = \strtolower($name);
        return $this;
    }

    /**
     * Get route name
     * 
     * @return NULL|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * If route path contain template regex 
     * 
     * @param bool $hasRegex
     */
    public function containsRegex(bool $hasRegex)
    {
        $this->hasRegex = $hasRegex;
        return $this;
    }

    /**
     * If route path contain template regex 
     * 
     * @return bool
     */
    public function hasRegex(): bool
    {
        return $this->hasRegex;
    }

    /**
     * Set http method of the route
     * 
     * @param string $method
     * @return \Router\Route
     */
    public function setHttpMethod(string $method)
    {
        if (empty($method))
        {
            throw new \InvalidArgumentException('HTTP Method cannot be empty.');
        }
        $this->httpMethod = \strtoupper($method);
        return $this;
    }

    /**
     * Get http method of the route
     * 
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * Set route uri path
     * 
     * @param string $path
     * @return \Router\Route
     */
    public function setPath(string $path)
    {
        if (empty($path))
        {
            throw new \InvalidArgumentException('URI Path cannot be empty.');
        }
        $this->path = \strtolower($path);
        return $this;
    }

    /**
     * Get route uri path
     * 
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set route options
     * 
     * @param array $options
     * @return \Router\Route
     */
    public function setOptions(array $options)
    {
        $this->options = \array_merge($this->options, $options);
        return $this;
    }

    /**
     * Get route options
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Add route option
     * 
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addOption(string $key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Get a route option
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = NULL)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get route unique id
     * 
     * @return int
     */
    public function getId(): int
    {
        return \crc32(\spl_object_hash($this));
    }

    /**
     * Set path segment param
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setPathSegmentParam(string $key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Get path segment param
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPathSegmentParam(string $key, $default = NULL)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Set route path template segment params
     * 
     * @param array $params
     * @return \Router\Route
     */
    public function setPathSegmentParams(array $params)
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }

    /**
     * Get route template path segment params
     * 
     * @return array
     */
    public function getPathSegmentParams(): array
    {
        return $this->params;
    }

}
