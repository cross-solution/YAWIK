<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use Auth\Factory\Form\RegisterFactory;
use Auth\Form\Register;
use Test\Bootstrap;

class RegisterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new RegisterFactory();
    }

    public function testInvoke()
    {
        $sm = clone Bootstrap::getServiceManager();
        $result = $this->testedObj->__invoke($sm,Register::class);
        $this->assertInstanceOf('Auth\Form\Register', $result);
    }
}
