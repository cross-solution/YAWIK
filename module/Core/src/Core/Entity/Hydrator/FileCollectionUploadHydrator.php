<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileCollectionUploadHydrator.php */ 
namespace Core\Entity\Hydrator;

use Core\Entity\FileInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class FileCollectionUploadHydrator implements HydratorInterface
{
    
    public function hydrate (array $value, $object)
    {
        if (!UPLOAD_ERR_OK == $value['error'] || !$object instanceOf FileInterface) {
            return null;
        }
    
        $object->setName($value['name'])
               ->setType($value['type'])
               ->setFile($value['tmp_name']);
    
        return $object;
    }
    
    public function extract($object)
    {
        if (!$object instanceOf FileInterface) {
            return null;
        }
        return $object->getId();
    }
    
}

