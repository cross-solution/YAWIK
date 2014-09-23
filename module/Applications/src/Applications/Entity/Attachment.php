<?php

namespace Applications\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Holds all attachments of an application.
 *
 * @author bleek@cross-solution.de
 *
 * @ODM\Document(collection="applications")
 */
class Attachment extends FileEntity {
    
    protected $uri;
    
    /**
     * get the URI of an attachment
     * @return string
     */
    function getUri(){
        return "/file/Applications.Attachment/" . $this->id . "/" .urlencode($this->name);
    }
    
}

?>