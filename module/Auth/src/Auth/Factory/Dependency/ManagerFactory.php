<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Factory\Dependency;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $manager = new \Auth\Dependency\Manager();
        $manager->setEventManager($serviceLocator->get('Auth/Dependency/Manager/Events'));
        
        return $manager;
    }
}
