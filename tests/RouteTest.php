<?php

namespace Isholao\Router\Tests;

use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{

    public function estShortcuts()
    {
        $r = new DummyRoutesCollection();
        $r->groupRoutes('/admin-',
                        function(DummyRoutesCollection $c)
        {
            $c->mapMany('GET', 'delete', 'delete');
        });

        $expected = [
            ['GET', '/admin-delete', 'delete'],
        ];

        $this->assertSame($expected, $r->routes);
    }

    public function testGroups()
    {
        $r = new \Router\RouteCollection();
        $r->delete('/delete', 'delete');
        $r->delete('/delete\?speak={lang=(?:en|de)}', 'delete');
        $r->groupRoutes('/group-one',
                        function (\Router\RouteCollection $r)
        {
            $r->delete('/delete', 'delete');
            $r->delete('/delete\?speak={lang=(?:en|de)}', 'delete');
        });

        $this->assertTrue($r->hasRoute('delete', '/group-one/delete?speak=en'));
    }

}