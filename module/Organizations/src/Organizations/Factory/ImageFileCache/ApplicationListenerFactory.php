<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\Factory\ImageFileCache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\ImageFileCache\ApplicationListener;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ApplicationListenerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $manager = $serviceLocator->get('Organizations\ImageFileCache\Manager');
        $repository = $serviceLocator->get('repositories')->get('Organizations/OrganizationImage');
        
        return new ApplicationListener($manager, $repository);
    }
}
