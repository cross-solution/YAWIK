<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating exception strategies
 */
class ExceptionStrategyFactory implements FactoryInterface
{
    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null)
    {
        switch ($canonicalName)
        {
            case 'unauthorizedaccesslistener':
                $listener = new \Auth\Listener\UnauthorizedAccessListener();
            break;
            
            case 'deactivateduserlistener':
                $listener = new \Auth\Listener\DeactivatedUserListener();
            break;
            
            default:
                throw new \InvalidArgumentException(sprintf('Unknown service %s', $canonicalName));
            break;
        }
        
        $config   = $serviceLocator->get('Config');
         
        if (isset($config['view_manager'])) {
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }
            if (isset($config['view_manager']['unauthorized_template'])) {
                $listener->setExceptionTemplate($config['view_manager']['unauthorized_template']);
            }
        }
        return $listener;
    }
}
