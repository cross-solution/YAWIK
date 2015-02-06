<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form;

use Auth\Form\Register;
use Zend\Form\Fieldset;

class RegisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Register
     */
    private $testedObject;

    public function setUp()
    {
        $this->testedObject = new Register();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Auth\Form\Register', $this->testedObject);

        $this->assertEquals($this->testedObject->count(), 3);
        $this->assertTrue($this->testedObject->has('register'));
        $this->assertTrue($this->testedObject->has('buttons'));
        $this->assertTrue($this->testedObject->has('csrf'));

        /** @var Fieldset $registerFieldset */
        $registerFieldset = $this->testedObject->get('register');
        $this->assertEquals($registerFieldset->count(), 3);
        $this->assertTrue($registerFieldset->has('name'));
        $this->assertTrue($registerFieldset->has('email'));
        $this->assertTrue($registerFieldset->has('role'));
    }

    public function testRegisterFieldset()
    {
        /** @var Fieldset $registerFieldset */
        $registerFieldset = $this->testedObject->get('register');

        $nameInput = $registerFieldset->get('name');
        $this->assertInstanceOf('Zend\Form\Element\Text', $nameInput);

        $emailInput = $registerFieldset->get('email');
        $this->assertInstanceOf('Zend\Form\Element\Email', $emailInput);

        $roleInput = $registerFieldset->get('role');
        $this->assertInstanceOf('Zend\Form\Element\Hidden', $roleInput);
    }

    public function testCsrfElement()
    {
        $csrfInput = $this->testedObject->get('csrf');
        $this->assertInstanceOf('Zend\Form\Element\Csrf', $csrfInput);
    }

    public function testSubmitElement()
    {
        /** @var Fieldset $buttons */
        $buttons = $this->testedObject->get('buttons');
        $buttonInput = $buttons->get('button');

        $this->assertInstanceOf('Zend\Form\Element\Submit', $buttonInput);
    }
}