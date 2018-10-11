<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\AbstractIdentifiableEntity;

/**
 * User Token Entity.
 *
 * This entity allows to define a tokens for user, by token you can
 * reset password and other things.
 *
 * @ODM\EmbeddedDocument
 */
class Token extends AbstractIdentifiableEntity
{

    /**
     * Token
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $hash;

    /**
     * Expiration date of token
     *
     * @var \Datetime
     * @ODM\Field(type="date")
     */
    protected $expirationDate;

    /**
     * @return \Datetime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \Datetime|string $expirationDate
     *
     * @return self
     */
    public function setExpirationDate($expirationDate)
    {
        if (is_string($expirationDate)) {
            $expirationDate = new \DateTime($expirationDate);
        }

        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }
}
