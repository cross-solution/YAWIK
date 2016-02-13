<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Form;

use Auth\Form\Login;
use Auth\Form\LoginInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\FormElementManager;

class LoginFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Login
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var FormElementManager $serviceLocator
         * @var LoginInputFilter $filter
         */
        $filter = $serviceLocator->getServiceLocator()->get('Auth\Form\LoginInputFilter');
        $form = new Login(null, array());
        $form->setInputfilter($filter);
        return $form;
    }
}
