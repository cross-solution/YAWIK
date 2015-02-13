<?php

namespace Auth\Form\SLFactory;

use Auth\Form\Register;
use Auth\Form\RegisterInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterSLFactory implements FactoryInterface
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
        $form->setInputfilter($filter);

        return $form;
    }
}