<?php

namespace Core\Mapper\Query\Service;

use Zend\ServiceManager\InitializerInterface;
use Core\Mapper\Query\Criteria\CriteriaInterface;

class CriteriaInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceOf CriteriaInterface) {
            return;
        }
        
        $instance->setCriterionPluginManager(
            $serviceLocator->get('query_criterion_manager')
        );
    }
	
}
