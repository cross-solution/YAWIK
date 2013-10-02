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
    
    
    public function __construct($repository, $fieldName = 'file')
    {
        $this->fieldName = $fieldName;
        $this->strategy = new \Core\Repository\Hydrator\FileUploadStrategy($repository);
        
    }
    
    public function extract($object) 
    {
       return $this->strategy->extract($object); 
    }
    
    public function hydrate(array $data, $object)
    {
        if (!isset($data[$this->fieldName])) {
            return $object;
        }
        return $this->strategy->hydrate($data[$this->fieldName]);
    }
}

