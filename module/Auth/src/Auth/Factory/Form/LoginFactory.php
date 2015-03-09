<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Form;

use Auth\Form\Login;
use Auth\Form\LoginInputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
         * @var $filter LoginInputFilter
         */
        $filter = $serviceLocator->get('Auth\Form\LoginInputFilter');
        $form = new Login(null, array());
        $form->setInputfilter($filter);
        return $form;
    }
}
