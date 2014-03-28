<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** StatusInterface.php */ 
namespace Applications\Entity;

use Core\Entity\EntityInterface;

interface StatusInterface extends EntityInterface
{
    const INCOMING  = 'incoming';
    const CONFIRMED = 'confirmed';
    const INVITED   = 'invited';
    const REJECTED  = 'rejected';
    
    public function __construct($status = self::INCOMING);
    public function getName();
    public function getOrder();
    public function __toString();
}

