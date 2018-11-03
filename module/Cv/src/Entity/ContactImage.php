<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Cv\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="cvs.contact.images")
 * @ODM\HasLifecycleCallbacks()
 */
class ContactImage extends FileEntity
{
    /**
     * @var Contact
     */
    protected $contact;
    

    /**
     * Gets the URI of an image
     *
     * The returned URI is NOT prepended with the base path!
     *
     * @return string
     */
    public function getUri()
    {
        return "/file/Cv.ContactImage/" . $this->id . "/" .urlencode($this->name);
    }
    
    /**
     * @ODM\PreRemove
     */
    public function preRemove()
    {
        $this->contact->setImage(null);
    }
    
    /**
     * @param Contact $contact
     * @return ContactImage
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
        
        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
