<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core MongoDb Mappers */
namespace Core\Repository\Mapper;

use Core\Repository\EntityBuilder\EntityBuilderInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Concrete implementation of \Core\Mapper\MongoDb\MapperInterface
 * 
 */
abstract class AbstractBuilderAwareMapper extends AbstractMapper implements EntityBuilderAwareInterface
{
    
    protected $builders;
     
    
    public function setEntityBuilderManager(ServiceLocatorInterface $entityBuilderManager)
    {
        $this->builders = $entityBuilderManager;
        return $this;
    }
    
    public function getEntityBuilderManager()
    {
        return $this->builders;
    }
    
    protected function getBuilder($builder)
    {
        if (!$builder instanceOf EntityBuilderInterface) {
            $builder = $this->builders->get($builder);
        }
        return $builder;
    }
    
    protected function buildEntity($data, $builder)
    {
        $builder = $this->getBuilder($builder);
        $entity = $builder->build($data);
        return $entity;
    }
    
    protected function buildCollection($data, $builder)
    {
        $builder = $this->getBuilder($builder);
        $collection = $builder->getCollection($data);
        return $collection;
    }
    
}