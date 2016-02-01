<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use Auth\Factory\Form\RegisterFactory;
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

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $fm = $sm->get('formElementManager');
        $result = $this->testedObj->createService($fm);
        $this->assertInstanceOf('Auth\Form\Register', $result);
    }
}