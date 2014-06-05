<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** PreUpdateAwareInterface.php */ 
namespace Core\Entity;

interface PreUpdateAwareInterface
{
    public function preUpdate($isNew = false);
}

