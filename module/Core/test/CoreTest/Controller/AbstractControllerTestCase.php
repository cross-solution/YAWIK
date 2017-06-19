<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Controller;

use Test\Bootstrap;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\Router\SimpleRouteStack;
use Zend\Router\Http\HttpRouterFactory as RouterFactory;
//use Zend\Router\RouterFactory;
use PHPUnit_Framework_ExpectationFailedException;

abstract class AbstractControllerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var AbstractActionController
     */
    protected $controller;

    public function init($controllerName, $actionName = 'index', $lang = 'en')
    {
        $this->routeMatch = new RouteMatch(
            array(
                'controller' => $controllerName,
                'action' => $actionName,
                'lang' => $lang
            )
        );
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);

        /** @var SimpleRouteStack $router */
        $routerFactory = new RouterFactory();
        $router        = $routerFactory->createService(clone Bootstrap::getServiceManager());
        $router->setDefaultParam('lang', $lang);

        $this->event->setRouter($router);
    }

    protected function assertResponseStatusCode($code)
    {
        /** @var Response $response */
        $response = $this->controller->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }

    /**
     * Get response header by key
     *
     * @param string $header
     *
     * @return \Zend\Http\Header\HeaderInterface|false
     */
    protected function getResponseHeader($header)
    {
        /** @var Response $response */
        $response = $this->controller->getResponse();
        $headers = $response->getHeaders();
        $responseHeader = $headers->get($header, false);
        return $responseHeader;
    }

    /**
     * Assert that response is a redirect
     */
    protected function assertRedirect()
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (false === $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response is a redirect'
            );
        }
        $this->assertNotEquals(false, $responseHeader);
    }

    /**
     * Assert that response is NOT a redirect
     */
    protected function assertNotRedirect()
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (false !== $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response is NOT a redirect, actual redirection is "%s"',
                    $responseHeader->getFieldValue()
                )
            );
        }
        $this->assertFalse($responseHeader);
    }

    /**
     * Assert that response redirects to given URL
     *
     * @param string $url
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertRedirectTo($url)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response is a redirect'
            );
        }
        if ($url != $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response redirects to "%s", actual redirection is "%s"',
                    $url,
                    $responseHeader->getFieldValue()
                )
            );
        }
        $this->assertEquals($url, $responseHeader->getFieldValue());
    }

    /**
     * Assert that response does not redirect to given URL
     *
     * @param string $url
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertNotRedirectTo($url)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response is a redirect'
            );
        }
        if ($url == $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response redirects to "%s"',
                    $url
                )
            );
        }
        $this->assertNotEquals($url, $responseHeader->getFieldValue());
    }

    /**
     * Assert that redirect location matches pattern
     *
     * @param string $pattern
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertRedirectRegex($pattern)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response is a redirect'
            );
        }
        if (!preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response redirects to URL MATCHING "%s", actual redirection is "%s"',
                    $pattern,
                    $responseHeader->getFieldValue()
                )
            );
        }
        $this->assertTrue((bool)preg_match($pattern, $responseHeader->getFieldValue()));
    }

    /**
     * Assert that redirect location does not match pattern
     *
     * @param string $pattern
     *
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertNotRedirectRegex($pattern)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if (!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                'Failed asserting response is a redirect'
            );
        }
        if (preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(
                sprintf(
                    'Failed asserting response DOES NOT redirect to URL MATCHING "%s"',
                    $pattern
                )
            );
        }
        $this->assertFalse((bool)preg_match($pattern, $responseHeader->getFieldValue()));
    }
    
    
}