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

