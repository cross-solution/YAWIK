<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace JobsTest\Options;

use Jobs\Options\ModuleOptions as Options;

class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    public function setUp()
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers Jobs\Options\ModuleOptions::getMultipostingApprovalMail
     * @covers Jobs\Options\ModuleOptions::setMultipostingApprovalMail
     */
    public function testSetGetMultipostingApprovalMail()
    {
        $mail="abc@mail.de";

        $this->options->setMultipostingApprovalMail($mail);
        $this->assertEquals($mail, $this->options->getMultipostingApprovalMail());
    }

    /**
     * @covers Jobs\Options\ModuleOptions::getDefaultLogo
     * @covers Jobs\Options\ModuleOptions::setDefaultLogo
     */
    public function testSetGetDefaultLogo()
    {
        $image="image.png";

        $this->options->setDefaultLogo($image);
        $this->assertEquals($image, $this->options->getDefaultLogo());
    }

    /**
     * @covers Jobs\Options\ModuleOptions::getMultipostingTargetUri
     * @covers Jobs\Options\ModuleOptions::setMultipostingTargetUri
     */
    public function testSetGetMultipostingTargetUri()
    {
        $uri="http://test.de/uri";
        $this->options->setMultipostingTargetUri($uri);
        $this->assertEquals($uri, $this->options->getMultipostingTargetUri());
    }

    /**
     * @covers Jobs\Options\ModuleOptions::getMultipostingTargetUser
     * @covers Jobs\Options\ModuleOptions::setMultipostingTargetUser
     */
    public function testSetGetMultipostingTargetUser()
    {
        $username='username';

        $this->options->setMultipostingTargetUser($username);
        $this->assertEquals($username, $this->options->getMultipostingTargetUser());
    }

    /**
     * @covers Jobs\Options\ModuleOptions::getMultipostingTargetPassword
     * @covers Jobs\Options\ModuleOptions::setMultipostingTargetPassword
     */
    public function testSetGetMultipostingTargetPassword()
    {
        $password="secret";

        $this->options->setMultipostingTargetPassword($password);
        $this->assertEquals($password, $this->options->getMultipostingTargetPassword());
    }
}
