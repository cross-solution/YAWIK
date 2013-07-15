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
interface EntityInterface 
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
    
    /**
     * Sets data.
     * 
     * The data should be given as an associative array where the
     * key is the property name and the value is the value.
     * 
     * @param array $data
     */
    public function setData(array $data);
}