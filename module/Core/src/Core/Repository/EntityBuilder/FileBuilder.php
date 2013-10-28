<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileBuilder.php */ 
namespace Core\Repository\EntityBuilder;

use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Core\Repository\Mapper\MapperInterface;

class FileBuilder extends EntityBuilder 
{
    
    protected $fileStoreName;
        
    /**
     * @return the $fileStoreName
     */
    public function getFileStoreName ()
    {
        return $this->fileStoreName;
    }

	/**
     * @param field_type $fileStoreName
     */
    public function setFileStoreName ($fileStoreName)
    {
        $this->fileStoreName = $fileStoreName;
        return $this;
    }

    public function build($data = array())
    {
        $dataArray = $data->file;
        $dataArray['name'] = $data->getFilename();
        $dataArray['size'] = $data->getSize();
        $dataArray['type'] = $data->file['mimetype'];

        $entity = parent::build($dataArray);
        $entity->injectUri(
            'http://' . $_SERVER['HTTP_HOST'] . '/file/' . $this->getFileStoreName() 
            . '/' . $entity->getId() . '/' . $entity->getName()
        );
        
        /*@todo needs better strategy!*/
        $entity->injectContent(function() use ($data) {
            return $data->getBytes();
        });
        
        $entity->injectResource(function() use ($data) {
            return $data->getResource();
        });
        
        
        return $entity;
        
    }
    
    /**
     * @todo Belongs in a strategy...
     * @param EntityInterface $entity
     */
    public function unbuild(EntityInterface $entity)
    {
        return $entity->getId();
    }
    
}
