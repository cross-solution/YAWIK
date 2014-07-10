<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Applications entities */
namespace Applications\Entity;

use Auth\Entity\Info;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Contact informations. 
 * 
 * @author cbleek
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class Contact extends Info {

    /**
     * profile image of an application.
     * 
     * As contact image is stored as an {@link Applications\Entity\Attachment} it must be 
     * redeclared here.
     * 
     * @var \Core\Entity\FileInterface
     * @ODM\ReferenceOne(targetDocument="Attachment", simple=true, nullable=true, cascade={"persist", "update", "remove"})
     */
    protected $image;
    
}

?>