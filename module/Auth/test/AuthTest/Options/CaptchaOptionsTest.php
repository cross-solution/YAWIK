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

use Auth\Options\CaptchaOptions as Options;
use Zend\Form\View\Helper\Captcha\ReCaptcha;

/**
 * Test the template entity.
 *
 * @covers \Auth\Options\ModuleOptions
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group Auth
 * @group Auth.Options
 */
class CaptchaOptionsTest extends TestCase
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
     * Do setter and getter methods work correctly?
     *
     * @param string $setter Setter method name
     * @param string $getter getter method name
     * @param mixed $value Value to set and test the getter method with.
     * @covers \Auth\Options\CaptchaOptions::getMode
     * @covers \Auth\Options\CaptchaOptions::setMode
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
            array('setMode', 'getMode', 'image'),
            array('setMode', 'getMode', 'reCaptcha'),

        );
    }

    /**
     * @covers \Auth\Options\CaptchaOptions::getImage
     * @covers \Auth\Options\CaptchaOptions::setImage
     */
    public function testSetGetImage()
    {
        $params = [
            'expiration' => '600',
            'wordlen' => '8',
            'font' => 'data/fonts/arial.ttf',
            'fontSize' => '30',
            'imgDir' => 'public/captcha',
            'imgUrl' => '/captcha'
            ];
        $target=$this->options;
        $target->setImage($params);

        $this->assertSame($target->getImage(), $params);
    }

    /**
     * @covers \Auth\Options\CaptchaOptions::getReCaptcha
     * @covers \Auth\Options\CaptchaOptions::setReCaptcha
     */
    public function testSetGetReCaptcha()
    {
        $params = new ReCaptcha();
        $target=$this->options;
        $target->setReCaptcha($params);
        $this->assertSame($target->getReCaptcha(), $params);
    }
}
