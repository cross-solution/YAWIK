<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class Attributes extends AbstractEntity
{
    /**
     * 
     * @var boolean
     * @ODM\Boolean
     */
    protected $privacyPolicy;
    
    /**
     * 
     * @var boolean
     * @ODM\Boolean
     */
    protected $carbonCopy;
    
    public function setAcceptedPrivacyPolice($flag)
    {
        $this->privacyPolicy = (bool) $flag;
        return $this;
    }
    
    public function getAcceptedPrivacyPolice()
    {
        return $this->privacyPolicy;
    }
    
    public function setSendCarbonCopy($flag)
    {
        $this->carbonCopy = (bool) $flag;
        return $this;
    }
    
    public function getSendCarbonCopy()
    {
        return $this->carbonCopy;
    }
}
