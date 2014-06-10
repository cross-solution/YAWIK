<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileEntity.php */ 
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Doctrine\Common\Collections\Collection;

/**
 * stores files in MongoGridFS into the collection "files". You can override this.
 * 
 * @ODM\Document(collection="files", repositoryClass="Core\Repository\File")
 * @ODM\InheritanceType("COLLECTION_PER_CLASS")
 */
class FileEntity extends AbstractIdentifiableEntity implements FileInterface
{
    /**
     * owner of an attachment. Typically this is the candidate who applies for a joboffer.
     *
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true) */
    protected $user;
    
    
    /**
     * Name of the attachment 
     *
     * @ODM\Field */
    protected $name;
    
    /**
     * mimetype of the attachment.
     *
     * @ODM\String */
    protected $mimetype;

    /**
     * Binary data of the Attachment.
     * 
    /** @ODM\File */
    protected $file;
    
    /** 
     * Used by MongoGridFS. We don't use this. We use $dateUploaded instead.
     * @ODM\Field */
    protected $uploadDate;
    
    /**
     * date of uploaded file
     * 
     * @ODM\Field(type="tz_date")
     */
    protected $dateUploaded;
    
    /** @ODM\Field */
    protected $length;
    
    /** @ODM\Field */
    protected $chunkSize;
    
    /** @ODM\Field */
    protected $md5;
    
    /** 
     * @var PermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions") 
     */
    protected $permissions;
    
    public function getResourceId()
    {
        return 'Entity/File';
    }
    
    public function setUser(UserInterface $user)
    {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        
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
    
    /**
     * return the binary data of an attachment
     * 
     * @see \Core\Entity\FileInterface::getContent()
     */
    public function getContent()
    {
        if ($this->file instanceOf \Doctrine\MongoDB\GridFSFile) {
            return $this->file->getMongoGridFSFile()->getBytes();
        }
        return null;
    }
    
    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }
    
    public function getPermissions()
    {
        if (!$this->permissions) {
            $perms = new Permissions();
            if ($this->user instanceOf UserInterface) {
                $perms->grant($this->user, PermissionsInterface::PERMISSION_ALL);
            }
            $this->setPermissions($perms);
        }
        return $this->permissions;
    }
}

