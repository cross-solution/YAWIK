<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Factory\Repository\Decorator;

use Applications\Repository\Decorator\HasApplied;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates a HasApplied decorator for Applications repository.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class HasAppliedFactory implements FactoryInterface
{
    /**
     * Creates a "HasApplied"-decorator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return HasApplied
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repositories = $serviceLocator->get('repositories');
        $applications = $repositories->get('Applications');
        $decorated    = new HasApplied($applications);

        return $decorated;

    }


}