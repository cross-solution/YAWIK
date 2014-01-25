<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileEntity.php */ 
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Doctrine\Common\Collections\Collection;

/**
 * 
 * @ODM\Document(collection="files")
 * @ODM\InheritanceType("COLLECTION_PER_CLASS")
 */
class FileEntity extends AbstractIdentifiableEntity implements FileInterface, ResourceInterface
{
    /** @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true) */
    protected $user;
    
    /** @ODM\ReferenceMany(targetDocument="\Auth\Entity\User", simple=true) */
    protected $allowedUsers;
    
    /** @ODM\Field */
    protected $name;
    
    /** @ODM\String */
    protected $mimetype;
    
    /** @ODM\File */
    protected $file;
    
    /** 
     * Used by MongoGridFS
     * @ODM\Field */
    protected $uploadDate;
    
    /**
     * 
     * @var unknown
     * @ODM\Field(type="tz_date")
     */
    protected $dateUploaded;
    
    /** @ODM\Field */
    protected $length;
    
    /** @ODM\Field */
    protected $chunkSize;
    
    /** @ODM\Field */
    protected $md5;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getResourceId()
    {
        return 'Entity/File';
    }
    
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setAllowedUsers(Collection $users)
    {
        $this->allowedUsers = $users;
        return $this;
    }
    
    public function getAllowedUsers()
    {
        return $this->allowedUsers;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getPrettySize()
    {
        return $this->length;
    }
    
    public function setType($mime)
    {
        $this->mimetype = $mime;
        return $this;
    }
    
    public function getType()
    {
        return $this->mimetype;
    }
    
    public function setDateUploaded(\DateTime $date = null)
    {
        $this->dateUploaded = $date;
        return $this;
    }
    
    public function getDateUploaded()
    {
        if (!$this->dateUploaded) {
            $this->setDateUploaded(new \DateTime());
        }
        return $this->dateUploaded;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile($file)
    {
        $this->setDateUploaded(new \DateTime());
        $this->file = $file;
        return $this;
    }
    
    public function getLength()
    {
        return $this->length;
    }
    
    public function getResource()
    {
        if ($this->file instanceOf \Doctrine\MongoDB\GridFSFile) {
            return $this->file->getMongoGridFSFile()->getResource();
        }
        return null;
    }
    
    public function getContent()
    {
        if ($this->file instanceOf \Doctrine\MongoDB\GridFSFile) {
            return $this->file->getMongoGridFSFile()->getContent();
        }
        return null;
    }
}

