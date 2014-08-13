<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\Timeline;
//use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * History of Names
 *
 * @ODM\EmbeddedDocument
 */
class OrganizationName extends Timeline implements OrganizationNameInterface {
    
    /**
     * name of the Organization
     * 
     * @var string
     * @ODM\String
     */
    protected $name;
    
    public function __construct($name = Null) 
    {
        if (!empty($name)) {
            $this->name = $name;
        }
        parent::__construct();
    }
    
    /**
     * @return the $name
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName ($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Sets the id.
     * 
     * @param mixed $id
     */
    public function setId($id) 
    {
        return $this;
    }
    
    /**
     * Gets the id.
     * 
     * @return mixed
     */
    public function getId()
    {
        return;
    }
    
    
}