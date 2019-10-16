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

use Auth\Form\ForgotPassword;
use Zend\Form\Fieldset;

class ForgotPasswordTest extends TestCase
{
    /**
     * @var ForgotPassword
     */
    private $testedObject;

    protected function setUp(): void
    {
        $this->testedObject = new ForgotPassword();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Auth\Form\ForgotPassword', $this->testedObject);

        $this->assertEquals($this->testedObject->count(), 3);
        $this->assertTrue($this->testedObject->has('identity'));
        $this->assertTrue($this->testedObject->has('buttons'));
        $this->assertTrue($this->testedObject->has('csrf'));
    }

    public function testIdentityElement()
    {
        $identityInput = $this->testedObject->get('identity');
        $this->assertInstanceOf('Zend\Form\Element\Text', $identityInput);
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
