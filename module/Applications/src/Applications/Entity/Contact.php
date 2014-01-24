<?php

namespace Applications\Entity;

use Auth\Entity\Info;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author cbleek
 *
 * @ODM\EmbeddedDocument
 */
class Contact extends Info {
    /**
     * 
     * @var unknown
     * @ODM/ReferenceOne(targetDocument="Attachment")
     */
    protected $image;
    
}

?>