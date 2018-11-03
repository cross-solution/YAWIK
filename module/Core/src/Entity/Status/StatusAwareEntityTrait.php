<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Status;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Trait implementing StatusAwareEntityInterface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait StatusAwareEntityTrait
{
    /**
     * The status of this entity.
     *
     * @ODM\EmbedOne(discriminatorField="_entity")
     * @var StatusInterface
     */
    private $status;

    public function setStatus($state)
    {
        /** @noinspection PhpUndefinedClassConstantInspection */
        $statusClass = static::STATUS_ENTITY_CLASS;

        if (is_string($state)) {
            $state = new $statusClass($state);
        }

        if (!$state instanceof $statusClass) {
            throw new \InvalidArgumentException(sprintf(
                'Expected object of type %s, but recieved %s instead.',
                $statusClass,
                get_class($state)
            ));
        }

        $this->status = $state;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function hasStatus($state = null)
    {
        /* @var StatusInterface $status */
        $status = $this->getStatus();

        if (null === $state) {
            return is_object($status);
        }

        return $status ? $status->is($state) : false;
    }
}
