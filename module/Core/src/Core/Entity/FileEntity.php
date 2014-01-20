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

/**
 * 
 * @ODM\Document(collection="files")
 * @InheritanceType("COLLECTION_PER_CLASS")
 */
class FileEntity extends AbstractIdentifiableEntity implements FileInterface, ResourceInterface
{
    /** @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true) */
    protected $user;
    
    /** @ODM\Field */
    protected $name;
    
    /** @ODM\String */
    protected $mimeType;
    
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
    protected $dateUpload;
    
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
        $this->mimeType = $mime;
        return $this;
    }
    
    public function getType()
    {
        return $this->mimeType;
    }
    
    public function setDateUpload(\DateTime $date = null)
    {
        $this->dateUpload = $date;
        return $this;
    }
    
    public function getDateUpload()
    {
        if (!$this->dateUpload) {
            $this->setDateUpload(new \DateTime());
        }
        return $this->dateUpload;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile($file)
    {
        $this->setDateUpload(new \DateTime());
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

