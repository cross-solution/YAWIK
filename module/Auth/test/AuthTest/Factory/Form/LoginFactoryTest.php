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

use Auth\Factory\Form\LoginFactory;
use CoreTest\Bootstrap;

class LoginFactoryTest extends TestCase
{
    /**
     * @var LoginFactory
     */
    private $testedObj;

    protected function setUp(): void
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
