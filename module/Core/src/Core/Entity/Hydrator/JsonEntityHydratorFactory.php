<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity\Hydrator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JsonEntityHydratorFactory implements FactoryInterface
{
    protected $hydrator;

    /**
     * Create the settings service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->hydrator = new JsonEntityHydrator();
        $entityHydrator = $this->getEntityHydrator();
        $this->hydrator->injectHydrator($entityHydrator);
        $this->prepareHydrator();
        return $this->hydrator;
    }

    protected function prepareHydrator() {
    }

    protected function getEntityHydrator() {
        return new EntityHydrator();
    }

}