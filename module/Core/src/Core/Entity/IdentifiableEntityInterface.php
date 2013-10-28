<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core models */
namespace Core\Entity;


/**
 * Model interface
 */
interface IdentifiableEntityInterface 
{

    /**
     * Sets the id.
     * 
     * @param mixed $id
     */
    public function setId($id);
    
    /**
     * Gets the id.
     * 
     * @return mixed
     */
    public function getId();
    
}