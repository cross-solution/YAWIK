<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Applications\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractEntity;

/**
 * Holds various attributes like "send me a carbon copy" or "i accept the privacy policy".
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 *
 * @ODM\EmbeddedDocument
 */
class Attributes extends AbstractEntity
{
    /**
     * Flag wether privacy policy is accepted or not.
     *
     * @var boolean
     * @ODM\Field(type="boolean")
     */
    protected $privacyPolicy;
    
    /**
     * Flag wether to send a carbon copy or not.
     *
     * @var boolean
     * @ODM\Field(type="boolean")
     */
    protected $carbonCopy;

    /**
     * Sets wether the privacy policy is accepted.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setAcceptedPrivacyPolicy($flag)
    {
        $this->privacyPolicy = (bool) $flag;
        return $this;
    }

    /**
     * Returns wether the privacy policy is accepted.
     *
     * @return bool
     */
    public function getAcceptedPrivacyPolicy()
    {
        return $this->privacyPolicy;
    }

    /**
     *
     *
     * @param $flag
     *
     * @return self
     */
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
