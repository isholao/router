
[![Build Status](https://travis-ci.org/isholao/router.svg?branch=master)](https://travis-ci.org/isholao/router)

Install
-------

To install with composer:

```sh
composer require isholao/router
```

Requires PHP 7.1 or newer.

Usage
-----

Here's a basic usage example:

```php
<?php

require '/path/to/vendor/autoload.php';

$c = \Isholao\Router\RouteCollection();
$c->get('/','get_all_users_responder');
$c->mapMany('GET|POST','/login','login_responder','login');
$c->disptach('GET','/login'); //return Route instance or null
```
### Defining routes

```php
$c = \Isholao\Router\RouteCollection();
$c->mapOne('GET', '/login', 'defined_responder');
$c->mapOne('GET', '/{lang=(?:en|de)}/login', 'defined_responder');
or
$c->mapMany('GET|POST', '/login, 'defined_responder');
```

You can also make an optional path segment by add a `?` after the defined segement.

```php
$c = \Isholao\Router\RouteCollection();
$c->mapOne('GET', '/something/{hash=[a-zA-Z]+}?', 'defined_responder');
or
$c->mapMany('GET|POST', '/login, 'defined_responder');
```

You can set a default value for a segment because each defined route returns a `\Router\Route` instance. Note this can only be done on `mapOne` method or any of the helper functions `get(), post(), head(), delete(), option(), put()`

```php
$c = \Isholao\Router\RouteCollection();
$c->mapOne('GET', '/something/{hash=[a-zA-Z]+}', 'defined_responder')->setParam('hash','asd8asdasd9');
or
$c->delete('/something/{hash=[a-zA-Z]+}', 'defined_responder')->setParam('hash','asd8asdasd9');
```

#### Route Groups

Additionally, you can specify routes inside of a group. All routes defined inside a group will have a common prefix.

For example, defining your routes as:

```php
$c = \Isholao\Router\RouteCollection();
$c->groupRoutes('/admin', function (\Isholao\Router\RouteCollectionInterface $r) {
    $r->mapOne('GET', '/do-something', 'handler'); // this becomes `/admin/do-something`
    $r->post('/do-something-else', function(){}); //  // this becomes `/admin/do-another-thing`
});
```

### Dispatching a URI

A URI is dispatched by calling the `dispatch()` method of the created `\Isholao\Router\RouteCollection` object. This method
accepts the HTTP method and a URI. 

```php
$c = \Isholao\Router\RouteCollection();
$c->groupRoutes('/admin', function (\Isholao\Router\RouteCollectionInterface $r) {
    $r->mapOne('GET', '/do-something', 'handler'); // this becomes `/admin/do-something`
    $r->post('/do-something-else', function(){}); //  // this becomes `/admin/do-another-thing`
});

$c->dispatch('GET','/admin/do-something'); // return a \Isholao\Router\Route instance or null
```

