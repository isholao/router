<?php

namespace Isholao\Router\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @author Ishola O <ishola.tolu@gmail.com>
 */
class RouteParserTest extends TestCase
{

    function testTemplate()
    {
        $p = new \Isholao\Router\RouteParser('/delete\?lang={lang=(?:en|de)}');
        $r = $p->parse([]);
        $this->assertSame([
            'segments' => ['lang' => '(?:en|de)'],
            'regex' => '/delete\?lang=(?<lang>(?:en|de))'
                ], $r);

        $rc = new \Isholao\Router\RouteCollection();
        $rc->delete('/delete\?lang={lang=(?:en|de)}?', 'demo')
                ->setPathSegmentParam('lang', 'gb');

        $route = $rc->dispatch('delete', '/delete?lang=');
        $this->assertSame($route->getPathSegmentParam('lang'), 'gb');
    }

    function testContainsRegexPattern()
    {
        $this->assertTrue(\Isholao\Router\RouteParser::containsRegexPattern('/delete\?lang={lang=(?:en|de)}'));
    }

    function testNotContainsRegexPattern()
    {
        $this->assertFalse(\Isholao\Router\RouteParser::containsRegexPattern('/delete\?lang=lang=(?:en|de)'));
    }

    function testParse()
    {
        $p = new \Isholao\Router\RouteParser('/delete\?lang={lang=:lang}');
        $r = $p->parse([':lang' => '(?:en|de)']);
        $this->assertSame(['segments' => ['lang' => ':lang'], 'regex' => '/delete\?lang=(?<lang>(?:en|de))'],
                          $r);
    }

    function testParseOptional()
    {
        $p = new \Isholao\Router\RouteParser('/delete\?lang={lang=:lang}?');
        $r = $p->parse([':lang' => '(?:en|de)']);
        $this->assertSame(['segments' => ['lang' => ':lang'], 'regex' => '/delete\?lang=(?<lang>(?:en|de))?'],
                          $r);
    }
    

}
