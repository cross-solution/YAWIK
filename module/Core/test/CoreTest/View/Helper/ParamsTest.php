<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\View\Helper;

use Zend\Mvc\MvcEvent;
use Core\View\Helper\Params as Helper;
use Zend\Router\Http\RouteMatch;
use Zend\Http\PhpEnvironment\Request;

class ParamsTest extends \PHPUnit_Framework_TestCase
{
    
    public function getHelper(MvcEvent $e=null)
    {
        if (null == $e) {
            $e = new MvcEvent();
        }
        return new Helper($e);
    }
    
    public function testHelperInvokationWithoutParamsReturnsHelperInstance()
    {
        $e = new MvcEvent();
        $helper = new Helper($e);
        
        $this->assertInstanceOf('Core\View\Helper\Params', $helper());
    }
    
    public function testHelperInvokationReturnsParamFromRouteIfPresentOrFromEventOrDefault()
    {
        $e = new MvcEvent();
        $routeMatch = new RouteMatch(array(
            'testParam1' => 'routeValue',
        ));
        $e->setRouteMatch($routeMatch);
        
        $e->setParam('testParam2', 'eventValue');
        
        $helper = new Helper($e);
        
        $this->assertEquals('routeValue', $helper('testParam1'));
        $this->assertEquals('eventValue', $helper('testParam2'));
        $this->assertEquals('defaultValue', $helper('testParam3', 'defaultValue'));
    }
    
    public function testFromFilesMethod()
    {
        $_FILES=array(
            'testFile1' => array(
                'name' => 'testfile1.txt',
                'type' => 'text/plain',
                'size' => 128,
                'tmp_name' => '/tmp/123456',
                'error' => UPLOAD_ERR_OK
            )
        );
        $r = new Request();
        
        $e = new MvcEvent();
        $e->setRequest($r);
        
        $helper = new Helper($e);
        
        $this->assertEquals($_FILES['testFile1'], $helper->fromFiles('testFile1'), 'testFile1 differs!');
        $this->assertEquals($_FILES, $helper->fromFiles(), 'Whole file array differs!');
        $this->assertEquals('default', $helper->fromFiles('not_there', 'default'), 'default value differs!');
        
    }
    
    public function testFromHeaderMethod()
    {
        $headers = "X-Test-Header-1: Header1Value\r\n"
                 . "X-Test-Header-2: Header2Value\r\n";
        
        $r = new Request();
        $r->setHeaders(\Zend\Http\Headers::fromString($headers));
        
        $e = new MvcEvent();
        $e->setRequest($r);
        
        $helper = new Helper($e);
        
        $header = $helper->fromHeader('X-Test-Header-1');
        $this->assertInstanceOf('\Zend\Http\Header\GenericHeader', $header);
        $this->assertEquals('Header1Value', $header->getFieldValue());
        
        $expect = array(
            'X-Test-Header-1' => 'Header1Value',
            'X-Test-Header-2' => 'Header2Value',
        );
        $this->assertEquals($expect, $helper->fromHeader());
        
        $this->assertEquals('default', $helper->fromHeader('X-Not-Here', 'default'));
    }
    
    public function testFromPostMethod()
    {
        $_POST = array(
            'test1' => 'value1',
            'test2' => 'value2',
        );
    
        $r = new Request();
    
        $e = new MvcEvent();
        $e->setRequest($r);
    
        $helper = new Helper($e);
        
        $this->assertEquals('value1', $helper->fromPost('test1'));
        $this->assertEquals($_POST, $helper->fromPost());
        $this->assertEquals('default', $helper->fromPost('not_there', 'default'));
    }
    
    public function testFromQueryMethod()
    {
        $_GET = array(
            'test1' => 'value1',
            'test2' => 'value2',
        );
        
        $r = new Request();
        
        $e = new MvcEvent();
        $e->setRequest($r);
        
        $helper = new Helper($e);
        
        $this->assertEquals('value1', $helper->fromQuery('test1'));
        $this->assertEquals($_GET, $helper->fromQuery());
        $this->assertEquals('default', $helper->fromQuery('not_there', 'default'));
    }
    
    public function testFromRouteMethod()
    {
        $e = new MvcEvent();
        $helper = new Helper($e);
        
        $this->assertNull($helper->fromRoute('test'));
        $this->assertEquals('default', $helper->fromRoute('test', 'default'));
        
        $params = array(
            'test' => 'value',
            'test1' => 'value1',
        );
        $rm = new RouteMatch($params);

        $e->setRouteMatch($rm);
        
        $this->assertEquals('value', $helper->fromRoute('test'));
        $this->assertEquals($params, $helper->fromRoute());
        $this->assertEquals('default', $helper->fromRoute('not_there', 'default'));
    }
    
}