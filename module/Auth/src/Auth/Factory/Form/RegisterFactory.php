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
use Auth\Options\CaptchaOptions;

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

        /* @var $config CaptchaOptions */
        $config = $serviceLocator->get('Auth/CaptchaOptions');

        $form = new Register(null, $config);

        $form->setAttribute('id', 'registration');

        $form->setInputfilter($filter);

        return $form;
    }
}
