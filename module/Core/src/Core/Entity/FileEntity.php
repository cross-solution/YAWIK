<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileEntity.php */ 
namespace Core\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\EntityInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class FileEntity extends AbstractIdentifiableEntity implements FileEntityInterface, ResourceInterface
{
    protected $user;
    protected $allowedUserIds;
    protected $name;
    protected $size;
    protected $type;
    protected $dateUploaded;
    protected $uri;
    protected $content;
    protected $contentCallback;
    protected $resource;
    protected $resourceCallback;

    public function getResourceId()
    {
        return 'Entity/File';
    }
	
    public function setUser(EntityInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
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
    
    public function addAllowedUser($user)
    {
        if (!is_array($user)) {
            $user = array($user);
        }
        
        $user = array_map(function($u) { return $u instanceOf UserInterface ? $u->getId() : $u; }, $user);
        
        foreach ($user as $u) {
            if (!in_array($user, $this->allowedUserIds)) {
                $this->allowedUserIds[] = $user;
            }
        }
        return $this;
    }
    
    public function removeAllowedUser($user=null)
    {
        if (null === $user) {
            $this->allowedUserIds = array();
            return $this;
        }
        
        if (!is_array($user)) {
            $user = array($user);
        }

        $user = array_map(function($u) { return $u instanceOf UserInterface ? $u->getId() : $u; }, $user);
        
        $this->allowedUserIds = array_filter(
            function($u) use ($user) {
                return !in_array($u, $user);
            },
            $this->allowedUserIds
        );
        
        return $this;
    }
    
	/**
     * @return the $name
     */
    public function getName ()
    {
        return $this->name;
    }

	/**
     * @param field_type $name
     */
    public function setName ($name)
    {
        $this->name = $name;
        return $this;
    }

	/**
     * @return the $size
     */
    public function getSize ()
    {
        return $this->size;
    }
    
    public function getPrettySize()
    {
        // determine multiplier
        $size = $this->getSize();
        if (1024 > $size) {
            return $size;
        }
        if (1048576 > $size) {
            return round( $size / 1024, 2) . ' kB'; 
        }
        if (1073741824 > $size) {
            return round( $size / 1048576, 2) . ' MB';
        }
        if (1.09951162778E+12 > $size) {
            return round( $size / 1073741824, 2) . ' GB';
        }
        return round ($size / 1.09951162778E+12, 2) . ' TB';
    }

	/**
     * @param field_type $size
     */
    public function setSize ($size)
    {
        $this->size = $size;
        return $this;
    }

	/**
     * @return the $type
     */
    public function getType ()
    {
        return $this->type;
    }

	/**
     * @param field_type $type
     */
    public function setType ($type)
    {
        $this->type = $type;
        return $this;
    }

	/**
     * @return the $dateUploaded
     */
    public function getDateUploaded ()
    {
        return $this->dateUploaded;
    }

	/**
     * @param field_type $dateUploaded
     */
    public function setDateUploaded (\DateTime $dateUploaded)
    {
        $this->dateUploaded = $dateUploaded;
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }
    
    public function injectUri($uri)
    {
        $this->uri = (string) $uri;
        return $this;
    }
	/**
     * @return the $content
     */
    public function getContent ()
    {
        if (!$this->content && is_callable($this->contentCallback)) {
            $this->putContent(call_user_func($this->contentCallback));
        }
        return $this->content;
    }

	/**
     * @param field_type $content
     */
    public function putContent ($content)
    {
        $this->content = $content;
        return $this;
    }

	/**
     * @param field_type $contentCallback
     */
    public function injectContent ($callable)
    {
        $this->contentCallback = $callable;
        return $this;
    }
    
    public function getInline ()
    {
        $content = $this->getContent();
        $filetype = $this->getType ();
        return 'data:image/' . $filetype . ';base64,' . base64_encode ($content);
    }
    
    
    public function getResource()
    {
        if (!$this->resource && is_callable($this->resourceCallback)) {
            $this->resource = call_user_func($this->resourceCallback);
        }
        return $this->resource;
    }
    
    public function injectResource($callable)
    {
        $this->resourceCallback = $callable;
        return $this;
    }

    
   
    
}

