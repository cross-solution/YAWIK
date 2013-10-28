<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Applications\Repository\Hydrator\Strategy\StatusStrategy;
use Core\Repository\Hydrator;
use Core\Repository\EntityBuilder\AbstractCopyableBuilderFactory;

class ApplicationBuilderFactory extends AbstractCopyableBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $cvBuilder = $serviceLocator->get($this->getBuilderName('application-cv'));
        $contactBuilder = $serviceLocator->get($this->getBuilderName('application-contact'));
        $attachmentsBuilder = $serviceLocator->get($this->getBuilderName('Core/File'));
        $historyBuilder = $serviceLocator->get($this->getBuilderName('Applications/History'));
        
        $hydrator = $this->getHydrator();

        $builder = new ApplicationBuilder(
            $hydrator, 
            new \Applications\Entity\Application(),
            new \Core\Entity\Collection()
        );
        
        $builder->addBuilder('cv', $cvBuilder)
                ->addBuilder('contact', $contactBuilder)
                ->addBuilder('attachments', $attachmentsBuilder, /*asCollection*/true)
                ->addBuilder('history', $historyBuilder, true);
        
        return $builder;
        
    }
    
    protected function getHydrator()
    {
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('dateCreated', new Hydrator\DatetimeStrategy())
                 ->addStrategy('dateModified', new Hydrator\DatetimeStrategy())
                 ->addStrategy('status', new StatusStrategy());
        return $hydrator;
    }

    
}