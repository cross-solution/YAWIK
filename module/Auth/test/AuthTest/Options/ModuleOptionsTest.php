<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Options;

use Auth\Options\ModuleOptions as Options;

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
     * @covers Auth\Options\ModuleOptions::getRole
     * @covers Auth\Options\ModuleOptions::setRole
     */
    public function testSetGetRole()
    {
        $input='user';
        $this->options->setRole($input);
        $this->assertEquals($input, $this->options->getRole());
    }

    /**
     * @covers Auth\Options\ModuleOptions::getFromEmail
     * @covers Auth\Options\ModuleOptions::setFromEmail
     */
    public function testSetGetFromEmail()
    {
        $input='user@example.com';
        $this->options->setFromEmail($input);
        $this->assertEquals($input, $this->options->getFromEmail());
    }

    /**
     * @covers Auth\Options\ModuleOptions::getFromName
     * @covers Auth\Options\ModuleOptions::setFromName
     */
    public function testSetGetFromName()
    {
        $input='Thomas Müller';
        $this->options->setFromName($input);
        $this->assertEquals($input, $this->options->getFromName());
    }



}
