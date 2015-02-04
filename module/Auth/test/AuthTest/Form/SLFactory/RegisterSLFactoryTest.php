<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form\SLFactory;

use Auth\Form\SLFactory\RegisterSLFactory;
use Test\Bootstrap;

class RegisterSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new RegisterSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Form\Register', $result);
    }
}