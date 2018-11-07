<?php

namespace Cv\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Form\CollectionContainer;
use Cv\Entity\Language;

class LanguageSkillCollectionFactory implements FactoryInterface
{
    /**
     * Create a CollectionContainer form
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return \Core\Form\CollectionContainer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $collectionContainer = new CollectionContainer('Cv/LanguageSkillForm', new Language());
        $collectionContainer->setLabel(/*@translate */ 'Additional Language Skills');
    
        return $collectionContainer;
    }
}
