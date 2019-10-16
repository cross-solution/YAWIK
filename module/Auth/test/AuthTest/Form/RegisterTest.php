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

use Auth\Form\Register;
use Auth\Options\CaptchaOptions;
use Zend\Form\Fieldset;

/**
* @covers \Auth\Form\Register
*/
class RegisterTest extends TestCase
{
    /**
     * @var Register
     */
    private $testedObject;

    protected function setUp(): void
    {
        $options = new CaptchaOptions();
        $this->testedObject = new Register(null, $options);
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
     
    public function testWithReCaptureField()
    {
        $options = new CaptchaOptions();
        $options->setMode("reCaptcha");
        $testedObject = new Register(null, $options);
        $captchaInput= $testedObject->get('captcha');
        $this->assertInstanceOf('Zend\Form\Element\Captcha', $captchaInput);
        $this->assertInstanceOf('Zend\Captcha\ReCaptcha', $captchaInput->getOption('captcha'));
    }
    
    public function testWithImageField()
    {
        if (! function_exists("imageftbbox")) {
            return $this->markTestSkipped('This test requires GD FT fonts support');
        }
        $options = new CaptchaOptions();
        $options->setMode("image");
        $testedObject = new Register(null, $options);
        $captchaInput= $testedObject->get('captcha');
        $this->assertInstanceOf('Zend\Captcha\Image', $captchaInput->getOption('captcha'));
    }
    
    public function testRoleValue()
    {
        $options = new CaptchaOptions();
        $testedObject = new Register(null, $options, 'user');
        $roleField = $testedObject->get('register')->get('role');
        $this->assertTrue($roleField->getValue() =='user');
    }
}
