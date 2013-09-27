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

use Core\Repository\RepositoryAwareInterface;

class FileBuilder extends EntityBuilder 
{
    
    
    public function build($data = array())
    {
        if (!$data instanceOf \MongoGridFSFile) {
            throw new \InvalidArgumentException('Instance of MongoGridFSFile expected.');
        }
        
        $dataArray = array(
            'name' => $data->getFilename(),
            'size' => $data->getSize(),
            'type' => $data->file['mimetype'],
            'dateUploaded' => $data->file['dateUploaded'],
        );
        $entity = parent::build($dataArray);
        
        /*@todo needs better strategy!*/
        $entity->setContentCallback(function() use ($data) {
            return $data->getBytes();
        });
        
        $entity->setResourceCallback(function() use ($data) {
            return $data->getResource();
        });
        
        return $entity;
        
    }
}

