<?php
/**
 * YAWIK
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
    public function __get($property);
    public function __set($property, $value);
    public function __isset($property);
  
}