<?php
namespace TheCodingMachine;


use Simplex\Container;
use Slim\App;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;
use Slim\Http\Uri;
use Slim\Interfaces\Http\EnvironmentInterface;

class TwigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        $simplex = new Container();
        $simplex->register(new TwigServiceProvider());

        $simplex['twig_directory'] = dirname(__DIR__);

        $twig = $simplex->get(\Twig_Environment::class);

        $result = $twig->render('tests/Fixtures/test.twig', [ 'name' => 'David' ]);
        $this->assertEquals('Hello David', $result);
    }
}
