<?php

namespace TheCodingMachine;

use Simplex\Container;

class TwigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        $simplex = new Container([new TwigServiceProvider()]);

        $simplex['twig_directory'] = dirname(__DIR__);

        $twig = $simplex->get(\Twig_Environment::class);

        $result = $twig->render('tests/Fixtures/test.twig', ['name' => 'David']);
        $this->assertEquals('Hello David', $result);
    }
}
