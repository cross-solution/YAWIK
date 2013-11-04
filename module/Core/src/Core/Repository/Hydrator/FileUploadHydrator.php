<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileUploadHydrator.php */ 
namespace Core\Repository\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class FileUploadHydrator implements HydratorInterface
{
    protected $repository;    
    
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    public function hydrate (array $data, $object)
    {
        $data['name'] = str_replace(' ', '_', $data['name']);
        $entityId = $this->repository->saveUploadedFile($data);
        $entity = $this->repository->find((string) $entityId);
        return $entity;
    }
    
    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\Strategy\StrategyInterface::hydrate()
    */
    
    public function extract($object) 
    {
       return $object->getId();
    }
    
}

