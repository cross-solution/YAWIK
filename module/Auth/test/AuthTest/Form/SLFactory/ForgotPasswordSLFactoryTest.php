<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form\SLFactory;

use Auth\Form\SLFactory\ForgotPasswordSLFactory;
use AuthTest\Bootstrap;

class ForgotPasswordSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPasswordSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ForgotPasswordSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Form\ForgotPassword', $result);
    }
}