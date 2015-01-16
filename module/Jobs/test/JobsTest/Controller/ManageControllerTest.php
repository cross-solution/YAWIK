<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace JobsTest\Controller;

use Jobs\Controller\ManageController;
use Test\Bootstrap;
use CoreTest\Controller\AbstractControllerTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\PluginManager;
use Zend\Stdlib\Parameters;

class ManageControllerTest extends AbstractControllerTestCase
{
    /**
    public function testIndexActionRedirects()
    {
        $this->dispatch('/');
        $this->assertTrue($response->getHeaders()->has('Location'));
    }
    */

}