<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileRepositoryAbstractFactory.php */ 
namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\Hydrator\EntityHydrator;
use Core\Repository\EntityBuilder\FileBuilderFactory as CoreFileBuilderFactory;
use Applications\Entity\FileEntity;

class FileBuilderFactory extends CoreFileBuilderFactory
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
    */
    public function createService(ServiceLocatorInterface $serviceLocator) 
    {
        $builder = parent::createService($serviceLocator);
        $builder->setEntityPrototype(new FileEntity());
        return $builder;
        
    }
    
    public function getHydrator()
    {
        
    }
}