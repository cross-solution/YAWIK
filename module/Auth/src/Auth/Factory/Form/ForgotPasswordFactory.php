<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Form;

use Auth\Form\ForgotPassword;
use Auth\Form\ForgotPasswordInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var $filter ForgotPasswordInputFilter
         */
        $filter = $serviceLocator->get('Auth\Form\ForgotPasswordInputFilter');

        $form = new ForgotPassword();
        $form->setInputfilter($filter);

        return $form;
    }
}
