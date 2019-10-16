<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form;

use PHPUnit\Framework\TestCase;

use Auth\Form\UserStatusContainer;
use Core\Form\Container;
use Core\Form\ViewPartialProviderInterface;

class UserStatusContainerTest extends TestCase
{
    /**
     * @var UserStatusContainer
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = new UserStatusContainer();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(UserStatusContainer::class, $this->container);
        $this->assertInstanceOf(Container::class, $this->container);
        $this->assertInstanceOf(ViewPartialProviderInterface::class, $this->container);
        $this->assertSame('auth/form/user-status-container', $this->container->getViewPartial());
    }
}
