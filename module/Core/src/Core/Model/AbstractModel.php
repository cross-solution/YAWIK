<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Model;


/**
 *
 */
class AbstractModel implements ModelInterface
{
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setData(array $data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}