<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Auth\Options;

use PHPUnit\Framework\TestCase;

use Auth\Options\ModuleOptions as Options;

/**
 * Test the template entity.
 *
 * @covers \Auth\Options\ModuleOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Auth
 * @group Auth.Options
 */
class ModuleOptionsTest extends TestCase
{
    /**
     * @var Options $options
     */
    protected $options;

    protected function setUp(): void
    {
        $options = new Options;
        $this->options = $options;
    }

    /**
     * @covers \Auth\Options\ModuleOptions::getRole
     * @covers \Auth\Options\ModuleOptions::setRole
     */
    public function testSetGetRole()
    {
        $input='user';
        $this->options->setRole($input);
        $this->assertEquals($input, $this->options->getRole());
    }

    /**
     * @covers \Auth\Options\ModuleOptions::getFromName
     * @covers \Auth\Options\ModuleOptions::setFromName
     */
    public function testSetGetFromName()
    {
        $input='Thomas Müller';
        $this->options->setFromName($input);
        $this->assertEquals($input, $this->options->getFromName());
    }

    /**
     * Do setter and getter methods work correctly?
     *
     * @param string $setter Setter method name
     * @param string $getter getter method name
     * @param mixed $value Value to set and test the getter method with.
     * @XXcovers Auth\Options\ModuleOptions::getEnableRegistration
     * @XXcovers Auth\Options\ModuleOptions::setEnableRegistration
     *
     * @dataProvider provideSetterTestValues
     */
    public function testSettingValuesViaSetterMethods($setter, $getter, $value)
    {
        $target = $this->options;

        if (is_array($value)) {
            $setValue = $value[0];
            $getValue = $value[1];
        } else {
            $setValue = $getValue = $value;
        }

        if (null !== $setter) {
            $object = $target->$setter($setValue);
            $this->assertSame($target, $object, 'Fluent interface broken!');
        }

        $this->assertSame($target->$getter(), $getValue);
    }


    /**
     * Provides datasets for testSettingValuesViaSetterMethods.
     *
     * @return array
     */
    public function provideSetterTestValues()
    {
        return array(
            array('setEnableRegistration', 'getEnableRegistration', false),
            array('setEnableRegistration', 'getEnableRegistration', true),
            array(null,'getEnableRegistration', true),
            array('setEnableResetPassword', 'getEnableResetPassword', false),
            array('setEnableResetPassword', 'getEnableResetPassword', true),
            array(null,'getEnableRegistration', true),
            array('setFromEmail', 'getFromEmail', 'test1@example.com'),
            array(null,'getFromEmail', 'email@example.com'),
            array('setAuthSuffix', 'getAuthSuffix', 'test1@example.com'),
            array(null,'getAuthSuffix', ''),
            array('setMailSubjectRegistration', 'getMailSubjectRegistration', 'Mail Subject'),
            array(null,'getMailSubjectRegistration', 'Welcome to YAWIK'),
        );
    }

    /**
     * @covers \Auth\Options\ModuleOptions::getEnableLogins
     * @covers \Auth\Options\ModuleOptions::setEnableLogins
     */
    public function testSetGetEnableLogins()
    {
        $input=['xing','linkedin'];
        $this->options->setEnableLogins($input);
        $this->assertEquals($input, $this->options->getEnableLogins());
    }
}
