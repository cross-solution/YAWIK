<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * defines AbstractOptions of the Auth Module
 */
class CaptchaOptions extends AbstractOptions
{

    const RE_CAPTCHA = 'reCaptcha';
    const IMAGE = 'image';

    /**
     * enable captcha feature. Possible values:
     * - none: captcha feature is disabled
     * - reCaptcha: Google reCaptcha service is used.
     * - image: local images are created
     *
     * @var string
     */
    protected $mode = 'none';

    /**
     * you have to create your private/public key.
     * See https://www.google.com/recaptcha/admin#list
     *
     * @var array
     */
    protected $reCaptcha = [
        'site_key' => 'Your Recapture Public Key',      // "site_key"
        'secret_key' => 'Your Recapture Private Key',    // "secret_key"
        'ssl' => true,                                    // include google api via http(s)
        ];

    /**
     * image mode creates local images by using the php gd extension
     *
     * @var array
     */
    protected $image = [
        'expiration' => '300',
        'wordlen' => '7',
        'font' => 'data/fonts/arial.ttf',
        'fontSize' => '20',
        'imgDir' => 'public/captcha',
        'imgUrl' => '/captcha'
        ];

    /**
     * @param $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param $reCaptcha
     * @return $this
     */
    public function setReCaptcha($reCaptcha)
    {
        $this->reCaptcha = $reCaptcha;
        return $this;
    }

    /**
     * @return array
     */
    public function getReCaptcha()
    {
        return $this->reCaptcha;
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return array
     */
    public function getImage()
    {
        return $this->image;
    }
}
