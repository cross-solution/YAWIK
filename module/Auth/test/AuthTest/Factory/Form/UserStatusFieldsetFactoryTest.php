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

use Auth\Factory\Form\UserStatusFieldsetFactory;
use Auth\Form\UserStatusFieldset;
use CoreTest\Bootstrap;

class UserStatusFieldsetFactoryTest extends TestCase
{
    /**
     * @var UserStatusFieldsetFactory
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new UserStatusFieldsetFactory();
    }

    public function testInvoke()
    {
        $sm = clone Bootstrap::getServiceManager();
        $result = $this->factory->__invoke($sm, UserStatusFieldset::class);
        $this->assertInstanceOf(UserStatusFieldset::class, $result);
    }
}
