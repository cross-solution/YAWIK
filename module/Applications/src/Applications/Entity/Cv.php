<?php

namespace Applications\Entity;

use Cv\Entity\Cv as BaseCv;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author cbleek
 *
 * @ODM\EmbeddedDocument
 */
class Cv extends BaseCv {
    
}

?>