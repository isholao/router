<?php

namespace Isholao\Router;

/**
 * @author Ishola O <ishola.tolu@outlook.com>
 */
class RouteCollection implements RouteCollectionInterface
{

    protected $groupPrefix = '';
    protected $namedRoutes = [];
    protected $httpMethods = [];
    protected static $allowedHttpMethods = [];
    protected static $segmentTemplate = [];
    protected $rules = [];
    protected $routeParser = NULL;

    public function __construct()
    {
        static::$allowedHttpMethods = ['POST', 'GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS',
            'PATCH'];
        static::$segmentTemplate = [
            ':num' => '[\d]+',
            ':all' => '.+',
            ':any' => '[^/]+',
            ':alpha' => '[a-zA-Z]+',
            ':alnum' => '[a-zA-Z\d]+'
        ];

        $this->routeParser = new RouteParser();
    }

    public static function addCustomHttpMethod(string $http_method)
    {
        if (empty($http_method))
        {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        static::$allowedHttpMethods[] = \strtoupper($http_method);
    }

    public function addRoute(RouteInterface $route)
    {
        $httpMethod = $route->getHttpMethod();
        if (!\in_array($httpMethod = \strtoupper($httpMethod),
                                                 static::$allowedHttpMethods))
        {
            throw new \InvalidArgumentException("Invalid rule '$httpMethod' http method.");
        }

        if (!isset($this->httpMethods[$httpMethod]))
        {
            $this->httpMethods[$httpMethod] = [];
        }

        $path = $this->groupPrefix . $route->getPath();

        if ($this->containsRegexPattern($path))
        {

            $this->routeParser->setTemplate($path);
            $result = $this->routeParser->parse(static::$segmentTemplate);
            if (@\preg_match("#^{$result['regex']}#ix", '') === FALSE)
            {
                throw new \InvalidArgumentException("Invalid rule template - '$path' translated to '{$result['regex']}'");
            }
            $route->setPath($result['regex']);
            $route->containsRegex(TRUE);
        } else
        {
            $route->setPath($path);
        }

        $id = $route->getId();
        $this->rules[$id] = $route;
        if ($route->getName())
        {
            $this->namedRoutes[$route->getName()] = $id;
        }
        $this->httpMethods[$httpMethod][$route->getPath()] = $id;
        return $route;
    }

    function patch(string $path, $responder, ?string $name = NULL,
                   array $data = []): RouteInterface
    {
        return $this->mapOne('PATCH', $path, $responder, $name, $data);
    }

    function get(string $path, $responder, ?string $name = NULL,
                 array $data = []): RouteInterface
    {
        return $this->mapOne('GET', $path, $responder, $name, $data);
    }

    function post(string $path, $responder, ?string $name = NULL,
                  array $data = []): RouteInterface
    {
        return $this->mapOne('POST', $path, $responder, $name, $data);
    }

    function put(string $path, $responder, ?string $name = NULL,
                 array $data = []): RouteInterface
    {
        return $this->mapOne('PUT', $path, $responder, $name, $data);
    }

    function delete(string $path, $responder, ?string $name = NULL,
                    array $data = []): RouteInterface
    {
        return $this->mapOne('DELETE', $path, $responder, $name, $data);
    }

    function options(string $path, $responder, ?string $name = NULL,
                    array $data = []): RouteInterface
    {
        return $this->mapOne('OPTIONS', $path, $responder, $name, $data);
    }

    function head(string $path, $responder, ?string $name = NULL,
                  array $data = []): RouteInterface
    {
        return $this->mapOne('HEAD', $path, $responder, $name, $data);
    }

    /**
     * Map a new uri rule
     * 
     * $router->mapOne('GET','/somepath', string or closure, 'name',['sadsa'=>'asdasd']);
     * </pre>
     * 
     * @param string $httpMethod HTTP Request method e.g GET
     * @param string $path Uri path of the rule
     * @param string|callable $responder Rule responder
     * @param string|NULL $name Name of the rule
     * @param array $data Route data
     * @return RouteInterface
     */
    public function mapOne(string $httpMethod, string $path, $responder,
                           ?string $name = NULL, array $data = []): RouteInterface
    {
        $this->validateHttpMethod($httpMethod);
        $this->validatePath($path);

        $route = (new Route)
                ->setPath($path)
                ->setResponder($responder)
                ->setHttpMethod($httpMethod)
                ->setOptions($data);

        if ($name)
        {
            $route->setName($name);
        }

        return $this->addRoute($route);
    }

    /**
     * Map a new uri rule
     * 
     * $router->map('GET|POST|OPTIONS','/somepath', string or closure, 'name',['sadsa'=>'asdasd']);
     * </pre>
     * 
     * @param string $httpMethods HTTP Request method e.g GET or methods using | to seperate them GET|POST|OPTIONS|HEAD
     * @param string $path Uri path of the rule
     * @param string|callable $responder Rule responder
     * @param array $data Route data
     * @return static
     */
    public function mapMany(string $httpMethods, string $path, $responder,
                            array $data = [])
    {
        $httpVerbs = \preg_split('#[|]+#', \strtoupper(\trim($httpMethods)), -1,
                                                             \PREG_SPLIT_NO_EMPTY);

        foreach ($httpVerbs as $httpVerb)
        {
            $this->mapOne($httpVerb, $path, $responder, NULL, $data);
        }
        return $this;
    }

    private function validateHttpMethod(string $httpMethod)
    {
        if (empty($httpMethod) || !\in_array($httpMethod,
                                             static::$allowedHttpMethods))
        {
            throw new \InvalidArgumentException('Invalid HTTP Method. Allowed methods are ' . \json_encode(static::$allowedHttpMethods));
        }
    }

    private function validatePath(string $path)
    {
        if (empty($path))
        {
            throw new \InvalidArgumentException('URI path template cannot be empty.');
        }
    }

    private function containsRegexPattern(string $template): bool
    {
        return (bool) \preg_match('#[{}]#', $template);
    }

    /**
     * Has route
     * 
     * <pre>
     * <b>Usage</b>
     * $routeCollection = new RouteCollection();
     * $routeCollection->get('/');
     * $routeCollection->hasRoute('GET','/');
     * </pre>
     * @param string $httpMethod Request method
     * @param string $uri Uri path
     * @return bool
     */
    function hasRoute(string $httpMethod, string $uri): bool
    {
        return $this->exists($httpMethod, $uri, FALSE);
    }

    /**
     * Get route 
     * 
     * @param string $httpMethod
     * @param string $uri
     * @return RouteInterface|NULL
     */
    function dispatch(string $httpMethod, string $uri): ?RouteInterface
    {
        return $this->exists($httpMethod, $uri, TRUE);
    }

    /**
     * 
     * @param string $httpMethod
     * @param string $uri
     * @param bool $return
     * @return bool|array
     */
    private function exists(string $httpMethod, string $uri, bool $return)
    {
        $httpMethod = \strtoupper($httpMethod);
        if (empty($uri))
        {
            throw new \InvalidArgumentException('URI cannot be empty.');
        }

        try
        {
            $this->validateHttpMethod($httpMethod);
        } catch (\Throwable $exc)
        {
            $exc = NULL;
            return $return ? NULL : FALSE;
        }

        if (!\array_key_exists($httpMethod, $this->httpMethods))
        {
            return $return ? NULL : FALSE;
        }

        $verb = $this->httpMethods[$httpMethod];
//no regex
        if (isset($verb[$uri]))
        {
            return $return ? $this->rules[$verb[$uri]] : TRUE;
        }


//assume regex
        foreach ($verb as $rule => $id)
        {
// Does the regex match?
            if (\preg_match("#^{$rule}$#ixu", $uri))
            {

                if ($return)
                {
                    static::processRoute($uri, $this->rules[$id]);
                    return $this->rules[$id];
                } else
                {
                    return true;
                }
            }
        }

        return $return ? NULL : FALSE;
    }

    /**
     * Add custom regex to the router
     * <pre>
     * $router->addRegex('hash','[0-9]{4}[A-Z]{3}[0-9]{4}');
     * 
     * usages:
     * 
     * $router->any('/product/{hashid=:hash}',...);
     * </pre>
     * @param string $name name of the expression
     * @param string $regex the expression
     * @return RoutesCollectionInterface
     */
    public static function addSegmentRegex(string $name, string $regex)
    {
        if (empty($name))
        {
            throw new \InvalidArgumentException('Regex name cannot be empty.');
        }

        if ($name[0] == ':')
        {
            $name = \substr($name, 1);
        }
        static::$segmentTemplate[":$name"] = $regex;
    }

    /**
     * Translate named template route
     * 
     * <pre>
     * <b>Usage:</b>
     * 
     * $router->map('GET','/person/{name=:any}',$callback,'person_name');
     * $router->getPathFor('person_name',['name'=>'the_name],'/demo/somthing');
     * 
     * <b>Result:</b>
     * 
     * /person/the_name
     * </pre>
     *  
     * @param string $name
     * @param array $params
     * @param string|NULL $default
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPathFor(string $name, array $params = [],
                               ?string $default = NULL): ?string
    {
        if (empty($name))
        {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }

        $k = \strtolower($name);
        if (isset($this->namedRoutes[$k]))
        {
            $route = $this->rules[$this->namedRoutes[$k]]->getPath();
//to avoid infinite loop
            $counter = 0;
            while (($start = \strpos($route, '{')) !== FALSE)
            {
                $end = \strpos($route, '}');
                $tmp = \substr($route, $start, ($end + 1) - $start);

                $opt = FALSE;
                //is this segment optional
                if (\substr($route, ($end + 1), 1) == '?')
                {
                    $opt = TRUE;
                }

                $parts = \explode('=', \str_replace(['{', '}'], '', $tmp));

                $name = \trim($parts[0]);
                $regex = \trim($parts[1]);

                $tmpRegex = NULL;
                //if regex begins with : then it might be a registered regex
                if ($regex[0] == ':' && isset(static::$segmentTemplate[$regex]))
                {
                    $tmpRegex = static::$segmentTemplate[$regex];
                }

                $re = $tmpRegex !== NULL ? $tmpRegex : $regex;

                if ($opt)
                {
                    $tmp .= '?';
                }

                //if name exists in the params
                if (isset($params[$name]))
                {
                    if (\preg_match("#^{$re}$#iu", $params[$name]))
                    {
                        $route = \str_replace($tmp, $params[$name], $route);
                    }
                }

                //if names doesn't exist but the regex is optional
                if (!isset($params[$name]) && $opt)
                {
                    $route = \str_replace($tmp, '', $route);
                }

                //prevent infinite loop
                if ($counter == 10)
                {
                    $route = $default;
                    break;
                }

                $counter++;
            }

            return $route;
        }
        return $default;
    }

    /**
     * 
     * @param string $groupPrefix
     * @param callable $callable
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function groupRoutes(string $groupPrefix, callable $callable)
    {
        if (empty($groupPrefix))
        {
            throw new \InvalidArgumentException("Group prefix cannot be empty.");
        }

        $self = clone $this;
        $currentGroupPrefix = $this->groupPrefix;
        $self->groupPrefix .= $groupPrefix;
        $callable($self);
        $this->httpMethods = \array_merge($this->httpMethods, $self->httpMethods);
        $this->namedRoutes = \array_merge($this->namedRoutes, $self->namedRoutes);
        foreach ($self->rules as $id => $obj)
        {
            $this->rules[$id] = $obj;
        }

        unset($self);
        $this->groupPrefix = $currentGroupPrefix;
        return $this;
    }

    /**
     * Process URI against a route
     * 
     * @param string $uri HTTP URL to process
     * @param Route  $route information to process against
     */
    protected static function processRoute(string $uri, RouteInterface &$route)
    {
        if (\preg_match('#^' . $route->getPath() . '$#i', $uri))
        {
            $template = [];
            \preg_replace_callback('#^' . $route->getPath() . '$#i',
                                   function (&$matches) use (&$route, &$template)
            {
                $m = '';
                $params = $route->getPathSegmentParams();
                unset($matches[0]); //remove searched url
                foreach ($matches as $k => &$v)
                {
                    //only get named subpattern
                    if (!\is_int($k))
                    {
                        $params[$k] = $m = $v;
                        $template['{' . $k . '}'] = $params[$k];
                    }
                }

                $route->setPathSegmentParams($params);
                return $m;
            }, $uri);


            /*
             * replace string responder with the route paramter 
             * /demo/{controller=blog} = {controller}Controller = blogController
             */
            if (\is_string($route->getResponder()))
            {
                $route->setResponder(\strtr($route->getResponder(), $template));
            }
        }
    }

}
