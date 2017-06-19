<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use Auth\Factory\Form\LoginFactory;
use Test\Bootstrap;

class LoginFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoginFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new LoginFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Form\Login', $result);
    }
}
