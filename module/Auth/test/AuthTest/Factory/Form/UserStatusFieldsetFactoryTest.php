<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use Auth\Factory\Form\UserStatusFieldsetFactory;
use Auth\Form\UserStatusFieldset;
use Test\Bootstrap;

class UserStatusFieldsetFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserStatusFieldsetFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new UserStatusFieldsetFactory();
    }

    public function testCreateService()
    {
		$sm = clone Bootstrap::getServiceManager();
		$fm = $sm->get('formElementManager');
		$result = $this->factory->createService($fm);
		$this->assertInstanceOf(UserStatusFieldset::class, $result);
    }
}
