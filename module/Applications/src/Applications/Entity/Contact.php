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
     * profile image of an application
     * 
     * @var unknown
     * @ODM\ReferenceOne(targetDocument="Attachment", simple=true, cascade={"persist", "remove"})
     */
    protected $image;
    
}

?>