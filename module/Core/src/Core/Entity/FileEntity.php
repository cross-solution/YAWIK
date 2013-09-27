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

class FileEntity extends AbstractIdentifiableEntity implements FileEntityInterface
{
    protected $name;
    protected $size;
    protected $type;
    protected $dateUploaded;
    protected $content;
    protected $contentCallback;
    protected $resource;
    protected $resourceCallback;
	
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

	/**
     * @return the $content
     */
    public function getContent ()
    {
        if (!$this->content && is_callable($this->contentCallback)) {
            $this->setContent(call_user_func($this->contentCallback));
        }
        return $this->content;
    }

	/**
     * @param field_type $content
     */
    public function setContent ($content)
    {
        $this->content = $content;
        return $this;
    }

	/**
     * @param field_type $contentCallback
     */
    public function setContentCallback ($callable)
    {
        $this->contentCallback = $callable;
        return $this;
    }
    
    public function getResource()
    {
        if (!$this->resource && is_callable($this->resourceCallback)) {
            $this->resource = call_user_func($this->resourceCallback);
        }
        return $this->resource;
    }
    
    public function setResourceCallback($callable)
    {
        $this->resourceCallback = $callable;
        return $this;
    }

    
   
    
}

