<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileRepositoryAbstractFactory.php */ 
namespace Core\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\Hydrator\EntityHydrator;

class FileBuilderFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
    */
    public function createService(ServiceLocatorInterface $serviceLocator) 
    {
        $hydrator = new EntityHydrator();
        $hydrator->addStrategy('dateUploaded', new \Core\Repository\Hydrator\DatetimeStrategy());
        
        $builder = new FileBuilder(
            $hydrator, 
            new \Core\Entity\FileEntity(),
            new \Core\Entity\Collection()
        );
        
        return $builder;
    }
}