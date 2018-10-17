<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileCollectionUploadHydrator.php */
namespace Core\Entity\Hydrator;

use Core\Entity\FileInterface;
use Zend\Hydrator\HydratorInterface;
use Doctrine\Common\Collections\Collection;

class FileCollectionUploadHydrator implements HydratorInterface
{
    protected $lastUploaded;
    protected $strategy;
    protected $elementName;

    /**
     * @param $name
     * @param $strategy
     */
    public function __construct($name, $strategy)
    {
        $this->strategy    = $strategy;
        $this->elementName = $name;
    }

    /**
     *
     *
     * @return \Core\Entity\FileInterface
     */
    public function getLastUploadedFile()
    {
        return $this->lastUploaded;
    }
    
    public function hydrate(array $value, $object)
    {
        if (!isset($value[$this->elementName]) || !UPLOAD_ERR_OK == $value[$this->elementName]['error'] || !$object instanceof Collection) {
            return null;
        }
    
        $file = $this->strategy->hydrate($value[$this->elementName]);
        if ($file) {
            $object->add($file);
            $this->lastUploaded = $file;
        }
        
        return $object;
    }
    
    public function extract($object)
    {
        if (!$object instanceof Collection) {
            return null;
        }
        
        $return = array();
        foreach ($object as $file) {
            $return[] = $file->getId();
        }
        return $return;
    }
}
