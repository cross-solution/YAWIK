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
use Core\Entity\Hydrator\EntityHydrator;
use Core\Repository\Hydrator\DatetimeStrategy;

class JsonFileBuilderFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
    */
    public function createService(ServiceLocatorInterface $serviceLocator) 
    {
        $hydrator = new EntityHydrator();
        $hydrator->addStrategy('dateUploaded', new DatetimeStrategy(DatetimeStrategy::FORMAT_MONGO, DatetimeStrategy::FORMAT_ISO));
        
        $builder = new JsonFileBuilder(
            $hydrator, 
            new \Core\Entity\FileEntity(),
            new \Core\Entity\Collection()
        );
        
        return $builder;
    }
}