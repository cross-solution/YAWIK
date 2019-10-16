<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use PHPUnit\Framework\TestCase;

use Auth\Factory\Form\RegisterFactory;
use Auth\Form\Register;
use CoreTest\Bootstrap;

class RegisterFactoryTest extends TestCase
{
    /**
     * @var RegisterFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new RegisterFactory();
    }

    public function testInvoke()
    {
        $sm = clone Bootstrap::getServiceManager();
        $result = $this->testedObj->__invoke($sm, Register::class);
        $this->assertInstanceOf('Auth\Form\Register', $result);
    }
}
