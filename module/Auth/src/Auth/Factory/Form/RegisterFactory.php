<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Form;

use Auth\Form\Register;
use Auth\Form\RegisterInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Register
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var $filter RegisterInputFilter
         */
        $filter = $serviceLocator->get('Auth\Form\RegisterInputFilter');
        $config = $serviceLocator->get('Config');
        $captchaConfig = isset($config['captcha']) ? $config['captcha'] : array();

        $form = new Register(null, array('captcha' => $captchaConfig));
        $form->setAttribute('id', 'registration');

        $form->setInputfilter($filter);

        return $form;
    }
}
