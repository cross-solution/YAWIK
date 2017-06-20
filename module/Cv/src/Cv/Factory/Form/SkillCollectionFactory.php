<?php

namespace Cv\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Form\CollectionContainer;

class SkillCollectionFactory implements FactoryInterface
{
    /**
     * Create a CollectionContainer form for skills
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return CollectionContainer
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = new CollectionContainer('CvSkillForm', new \Cv\Entity\Skill());
        $container->setLabel(/*@translate */ 'Skills');

        return $container;
    }
}
