<?php

namespace Applications\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author cbleek
 *
 * @ODM\Document(Collection="applications")
 */
class Attachment extends FileEntity {
    
}

?>