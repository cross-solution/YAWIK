<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Status extends AbstractEntity implements StatusInterface
{
    /**
     * status values
     */
    protected static $orderMap = [
        self::NONPUBLIC => 10,
        self::PUBLIC_TO_ALL => 20
    ];

    /**
     * name of the status
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * integer for ordering states.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $order;

    /**
     * @see \Cv\Entity\StatusInterface::__construct()
     */
    public function __construct($status = self::NONPUBLIC)
    {
        if (!isset(static::$orderMap[$status])) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        
        $this->name = $status;
        $this->order = $this->getOrder();
    }

    /**
     * @see \Cv\Entity\StatusInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see \Cv\Entity\StatusInterface::getOrder()
     */
    public function getOrder()
    {
        return self::$orderMap[$this->getName()];
    }

    /**
     * @see \Cv\Entity\StatusInterface::__toString()
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @see \Cv\Entity\StatusInterface::getStates()
     */
    public function getStates(array $exclude = [])
    {
        $states = self::$orderMap;
        
        if ($exclude) {
            $states = array_diff_key($states, array_flip($exclude));
        }
        
        asort($states, SORT_NUMERIC);
        return array_keys($states);
    }
}
