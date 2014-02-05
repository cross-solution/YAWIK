<?php

namespace Applications\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author cbleek
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
        return "/file/Applications.Attachment/" . $this->id . "/" .$this->name;
    }
    
}

?>