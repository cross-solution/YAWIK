<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * A publisher pushes job postings
 *
 * @ODM\EmbeddedDocument
 */
class Publisher extends AbstractEntity
{
    /**
     * Host of the publisher.
     *
     * @var $host string
     * @ODM\Field(type="string")
     */
    protected $host;

    /**
     * external Reference
     *
     * @var $reference string
     * @ODM\Field(type="string")
     */
    protected $reference;

    /**
     * external id of a publisher
     *
     * @var $externalId string
     * @ODM\Field(type="string")
     */
    protected $externalId;

    /**
     * @param $host string
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param $externalId
     *
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
}
