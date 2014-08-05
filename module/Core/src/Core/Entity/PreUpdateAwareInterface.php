<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PreUpdateAwareInterface.php */ 
namespace Core\Entity;

interface PreUpdateAwareInterface
{
    public function preUpdate($isNew = false);
}

