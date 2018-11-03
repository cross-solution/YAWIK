<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
    
namespace Jobs\Entity;

use Core\Entity\EntityTrait;
use Core\Entity\Status\AbstractSortableStatus;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Job status entity
 *
 * @ODM\EmbeddedDocument
 */
class Status extends AbstractSortableStatus implements StatusInterface
{
    use EntityTrait;

    /**
     * status values
     */
    protected static $sortMap = array(
        self::CREATED => 10,
        self::WAITING_FOR_APPROVAL => 20,
        self::REJECTED => 30,
        self::PUBLISH => 40,
        self::ACTIVE => 50,
        self::INACTIVE => 60,
        self::EXPIRED => 70,
    );

    /**
     * name of the job status
     *
     * @var string
     * @ODM\Field(type="string")
     * @deprecated since 0.29, replaced by AbstractStatus::$state
     */
    protected $name;

    /**
     * integer for ordering states.
     *
     * @var int
     * @ODM\Field(type="int")
     * @deprecated since 0.29, replaced by AbstractSortableStatus::$sort
     */
    protected $order;

    public function __construct($status = self::CREATED)
    {
        parent::__construct($status);

        $constant = 'self::' . strtoupper(str_replace(' ', '_', $status));
        if (!defined($constant)) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        $this->name=constant($constant);
        $this->order=$this->getOrder();
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getName()
     * @return String
     * @deprecated since 0,29, use __toString()
     */
    public function getName()
    {
        return isset($this->name)?$this->name:'';
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getOrder()
     * @return Int
     * @deprecated since 0,29, no replacement.
     */
    public function getOrder()
    {
        return self::$sortMap[$this->getName()];
    }

    /**
     * @todo remove this some versions after 0.29
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->state) {
            $this->state = $this->name;
        }
        return parent::__toString();
    }

    /**
     * @todo remove this some versions after 0.29
     *
     * @param object|string $state
     *
     * @return bool
     */
    public function is($state)
    {
        if (!$this->state) {
            $this->state = $this->name;
        }

        return parent::is($state);
    }
}
