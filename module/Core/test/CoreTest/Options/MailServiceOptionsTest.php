<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Options;

use Core\Options\MailServiceOptions as Options;

/**
 *
 * @covers \Core\Options\MailServiceOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Options
 */
class MailServiceOptionsTest extends \PHPUnit_Framework_TestCase
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
     * @covers Core\Options\MailServiceOptions::getUsername
     * @covers Core\Options\MailServiceOptions::setUsername
     */
    public function testSetGetUsername()
    {
        $this->options->setUsername('foo');
        $this->assertEquals('foo', $this->options->getUsername());
    }

    /**
     * @covers Core\Options\MailServiceOptions::getPassword
     * @covers Core\Options\MailServiceOptions::setPassword
     */
    public function testSetGetPassword()
    {
        $this->options->setPassword('bar');
        $this->assertEquals('bar', $this->options->getPassword());
    }

    /**
     * @covers Core\Options\MailServiceOptions::getSsl
     * @covers Core\Options\MailServiceOptions::setSsl
     */
    public function testSetGetSsl()
    {
        $this->options->setSsl("tls");
        $this->assertEquals("tls", $this->options->getSsl());
    }

    /**
     * @covers Core\Options\MailServiceOptions::getTransportClass
     * @covers Core\Options\MailServiceOptions::setTransportClass
     *
     * We use "sendmail" as default, because authentication has to be enabled when
     * using "smtp".
     */
    public function testSetGetTransportClass()
    {
        $this->assertEquals("sendmail", $this->options->getTransportClass());
        $this->options->setTransportClass("smtp");
        $this->assertEquals("smtp", $this->options->getTransportClass());
    }
}
