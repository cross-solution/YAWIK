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

/**
 * 
 * @ODM\Document(collection="files")
 * @InheritanceType("COLLECTION_PER_CLASS")
 */
class FileEntity extends AbstractIdentifiableEntity implements FileInterface
{
    /** @ODM\Field */
    protected $name;
    
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
    
    /** @ODM\Collection */
    protected $allowedUserIds;
    
    public function getId()
    {
        return $this->id;
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
    
    public function getAllowedUserIds()
    {
        return $this->allowedUserIds;
    }
    
    public function setAllowedUserIds(array $ids)
    {
        $this->allowedUserIds = $ids;
        return $this;
    }
    
    public function addAllowedUser($userOrId)
    {
        if ($userOrId instanceOf UserInterface) {
            $userOrId = $userOrId->getId();
        }
        
        if (!in_array($userOrId, $this->allowedUserIds)) {
            $this->allowedUserIds[] = $userOrId;
        }
    }
    
    public function removeAllowedUser($userOrId)
    {
        if ($userOrId instanceOf UserInterface) {
            $userOrId = $userOrId->getId();
        }
        
        $allowedUserIds = array();
        foreach ($this->allowedUserIds as $id) {
            if ($id != $userOrId) {
                $allowedUserIds[] = $id;
            }
        }
        
        return $this->setAllowedUserIds($allowedUserIds);
    }
    
    public function setDateUpload(\DateTime $date = null)
    {
        $this->dateUpload = $date;
        return $this;
    }
    
    public function getDateUpload()
    {
        return $this->dateUpload();
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    
    public function getResource()
    {
        return $this->getFile()->getResource();
    }
    
    public function getContent()
    {
        return $this->getFile()->getContent();
    }
}

