<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Factory\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\View\Helper\SocialButtons;

class SocialButtonsFactory implements FactoryInterface {

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SocialButtons
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\View\HelperPluginManager */
        $options = $serviceLocator->getServiceLocator()->get('Auth/Options');
        $config = $serviceLocator->getServiceLocator()->get('Config');
        $helper = new SocialButtons($options,$config);
        return $helper;
    }
}

