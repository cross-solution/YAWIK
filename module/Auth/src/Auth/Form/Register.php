<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Auth\Entity\User;
use Core\Form\ButtonsFieldset;
use Core\Form\Form;
use Zend\Captcha\Image;
use Zend\Captcha\ReCaptcha;
use Zend\Form\Fieldset;

class Register extends Form
{
    public function __construct($name = 'register-form', $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('data-handle-by', 'native');
        $this->setAttribute('class', 'form-horizontal');

        $fieldset = new Fieldset('register');
        $fieldset->setOptions(array('renderFieldset' => true));

        $fieldset->add(
            array(
                'type' => 'text',
                'name' => 'name',
                'options' => array(
                    'label' => /*@translate*/ 'Name',
                ),
            )
        );

        $fieldset->add(
            array(
                'type' => 'email',
                'name' => 'email',
                'options' => array(
                    'label' => /*@translate*/ 'Email',
                ),
            )
        );

        $fieldset->add(
            array(
                'name' => 'role',
                'type' => 'hidden',
                'attributes' => array(
                    'value' => User::ROLE_RECRUITER,
                ),
            )
        );

        $this->add($fieldset);

        if (($captchaOptions = $this->getOption('captcha')) && !empty($captchaOptions['use'])) {
            if ($captchaOptions['use'] === 'image' && !empty($captchaOptions['image'])) {
                $captcha = new Image($captchaOptions['image']);
            } elseif ($captchaOptions['use'] === 'reCaptcha' && !empty($captchaOptions['reCaptcha'])) {
                $captcha = new ReCaptcha($captchaOptions['reCaptcha']);
            }

            if (!empty($captcha)) {
                $this->add(array(
                    'name' => 'captcha',
                    'options' => array(
                        'label' => /*@translate*/ 'Are you human?',
                        'captcha' => $captcha,
                    ),
                    'type' => 'Zend\Form\Element\Captcha',
                ));
            }
        }

        $buttons = new ButtonsFieldset('buttons');
        $buttons->add(
            array(
                'type' => 'submit',
                'name' => 'button',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => /*@translate*/ 'Register',
                    'class' => 'btn btn-primary'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'csrf',
                'type' => 'csrf',
                'options' => array(
                    'csrf_options' => array(
                        'salt' => str_replace('\\', '_', __CLASS__),
                        'timeout' => 3600
                    )
                )
            )
        );

        $this->add($buttons);
    }
}