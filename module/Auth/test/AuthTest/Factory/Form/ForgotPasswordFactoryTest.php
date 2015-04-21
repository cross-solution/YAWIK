<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Form;

use Auth\Factory\Form\ForgotPasswordFactory;
use Test\Bootstrap;

class ForgotPasswordFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPasswordFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ForgotPasswordFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Form\ForgotPassword', $result);
    }
}