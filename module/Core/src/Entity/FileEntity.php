<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@xenon>
 * @author Rafal Ksiazek <harpcio@gmail.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

/** FileEntity.php */
namespace Core\Entity;

use Auth\Entity\User;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\UserInterface;

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
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id") */
    protected $user;
    
    
    /**
     * Name of the attachment
     *
     * @ODM\Field */
    protected $name;
    
    /**
     * mimetype of the attachment.
     *
     * @ODM\Field(type="string") */
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

    /**
     * @return string
     */
    public function getResourceId()
    {
        return 'Entity/File';
    }

    /**
     * Sets the owner of a file
     *
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        
        return $this;
    }

    /**
     * Gets the owner of a file
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the name of a file
     *
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the name of a file
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the length of file in GB, MB ot kB format
     *
     * @return string
     */
    public function getPrettySize()
    {
        $size = $this->getLength();
        
        if ($size >= 1073741824) {
            return round($size / 1073741824, 2) . ' GB';
        }
        
        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }
        
        if ($size >= 1024) {
            return round($size / 1024, 2) . ' kB';
        }
        
        return (string)$size;
    }

    /**
     * Sets the mime-type of a file
     *
     * @param $mime
     * @return $this
     */
    public function setType($mime)
    {
        $this->mimetype = $mime;
        return $this;
    }

    /**
     * Gets the mime-type of a file
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->mimetype;
    }

    /**
     * Sets the uploaded date of a file
     *
     * @param \DateTime|null $date
     * @return $this
     */
    public function setDateUploaded(\DateTime $date = null)
    {
        $this->dateUploaded = $date;
        return $this;
    }

    /**
     * Gets the uploaded date of a file
     *
     * @return mixed
     */
    public function getDateUploaded()
    {
        if (!$this->dateUploaded) {
            $this->setDateUploaded(new \DateTime());
        }
        return $this->dateUploaded;
    }

    /**
     * Gets the file
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the file
     *
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->setDateUploaded(new \DateTime());
        $this->file = $file;
        return $this;
    }

    /**
     * Gets the length of the file
     *
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Gets the resource of a file
     *
     * @return null|\stream
     */
    public function getResource()
    {
        if ($this->file instanceof \Doctrine\MongoDB\GridFSFile) {
            return $this->file->getMongoGridFSFile()->getResource();
        }
        return null;
    }
    
    /**
     * Returns the binary data of a file
     *
     * @see \Core\Entity\FileInterface::getContent()
     * @return null|string
     */
    public function getContent()
    {
        if ($this->file instanceof \Doctrine\MongoDB\GridFSFile) {
            return $this->file->getMongoGridFSFile()->getBytes();
        }
        return null;
    }

    /**
     * Sets Permissions of a file
     *
     * @param PermissionsInterface $permissions
     * @return $this
     */
    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Gets Permissions of a file
     *
     * @return PermissionsInterface
     */
    public function getPermissions()
    {
        if (!$this->permissions) {
            $perms = new Permissions();
            if ($this->user instanceof UserInterface) {
                $perms->grant($this->user, PermissionsInterface::PERMISSION_ALL);
            }
            $this->setPermissions($perms);
        }
        return $this->permissions;
    }
    
    /**
     * @see \Core\Entity\FileInterface::getUri()
     * @since 0.27
     */
    public function getUri()
    {
    }
}
