<?php

namespace Applications\Repository\EntityBuilder;



use Core\Repository\Hydrator\DatetimeStrategy;
use Applications\Repository\Hydrator\Strategy\StatusNameStrategy;
use Core\Entity\Hydrator\InjectAwareEntityHydrator as EntityHydrator;
use Zend\ServiceManager\ServiceLocatorInterface;

class JsonApplicationBuilderFactory extends ApplicationBuilderFactory
{
	
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $builder = parent::createService($serviceLocator);
        $builder->setExtractRelations(true, /*recursive*/ true);
        $jobBuilder = $serviceLocator->get('jsonJob');
        $builder->addBuilder('job', $jobBuilder);
        return $builder;
    }
    
    protected function getHydrator()
    {
        $hydrator = new EntityHydrator(array('attachments', 'job'));
        $strategy = new DatetimeStrategy(DatetimeStrategy::FORMAT_MONGO, DatetimeStrategy::FORMAT_ISO);
        $hydrator->addStrategy('dateCreated', $strategy)
                 ->addStrategy('dateModified', $strategy)
                 ->addStrategy('status', new StatusNameStrategy(StatusStrategy::EXTRACT_NAME));
        return $hydrator;
    }
    
    protected function getBuilderName($builderName)
    {
        if ('Core/File' == $builderName) {
            return 'Core/JsonFile';
        }
        if ('Applications/History' == $builderName) {
            return 'Applications/JsonHistory';
        }
        return 'json-' . $builderName;
    }

    
}