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
        $dataArray = array(
            '_id' => $data->file['_id'],
            'name' => $data->getFilename(),
            'size' => $data->getSize(),
            'type' => $data->file['mimetype'],
            'dateUploaded' => $data->file['dateUploaded'],
            //'uri' => $_SERVER['HTTP_HOST'] . '/file/' . $this->getFileStoreName() . '/' . $data->getFilename(),
        );
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
