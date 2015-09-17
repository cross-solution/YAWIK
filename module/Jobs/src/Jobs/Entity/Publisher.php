<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Holds various fields a o job opening template
 *
 * @ODM\EmbeddedDocument
 */
class Publisher extends AbstractEntity
{

    /**
     * Qualification field of the job template
     *
     * @var host
     * @ODM\String
     */
    protected $host;

    /**
     * externe Reference
     *
     * @var reference
     * @ODM\String
     */
    protected $reference;

    /**
     * externe externalId
     *
     * @var externalId
     * @ODM\String
     */
    protected $externalId;


    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }
}
