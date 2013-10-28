<?php

namespace Cv\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Core\Paginator\Adapter\MongoCursor as MongoCursorAdapter;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;

class Cv extends AbstractRepository implements EntityBuilderAwareInterface
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
	
	public function find($id, $mode = self::LOAD_LAZY)
    {
        $entity = $mode == self::LOAD_EAGER
                ? $this->getMapper('cv')->find($id)
                : $this->getMapper('cv')->find(
                      $id, 
                      array('educations', 'employments'),
                      /*exclude*/ true
                  );
        return $entity;
    }
    
    public function getPaginatorAdapter(array $params)
    {
    
        if (isset($params['sort'])) {
            $sort = $params['sort'];
            unset($params['sort']);
        } else {
            $sort = array();
        }
    	$query = $params;
    	#foreach ($propertyFilter as $property => $value) {
    #		if (in_array($property, array('Id'))) {
   # 			$query[$property] = new \MongoRegex('/^' . $value . '/');
   # 		}
   # 	}
    	$cursor = $this->getMapper('cv')->getCursor($query); //, array('cv'), true);
    	$cursor->sort($sort);
    	return new MongoCursorAdapter($cursor, $this->builders->get('cv'));
    }
    
    public function save(EntityInterface $entity)
    {
        $this->getMapper('cv')->save($entity);
    }
    
    
}