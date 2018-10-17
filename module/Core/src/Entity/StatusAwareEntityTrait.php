<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

/**
 * Implementation of the StatusAwareEntityInterface.
 *
 * @property-read string $statusEntity FQCN of the Status entity class.
 * @property-read string|StatusInterface $statusDefault Default status entity or name,
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait StatusAwareEntityTrait
{

    /**
     * The status entity.
     *
     * @var StatusInterface
     */
    private $status;

    public function getStatus()
    {
        if (!$this->status) {
            $this->setStatus(null);
        }

        return $this->status;
    }

    public function setStatus($status)
    {
        if (!$status instanceof StatusInterface) {
            $status = $this->createStatusFromName($status);
        }

        $this->status = $status;

        return $this;
    }

    public function hasStatus($status)
    {
        return $this->getStatus()->is($status);
    }

    /**
     * Create a status from the status name.
     *
     * @param string|null $status
     *
     * @return StatusInterface
     * @throws \RuntimeException if the property $statusEntity is not defined.
     */
    private function createStatusFromName($status = null)
    {
        if (!property_exists($this, 'statusEntity')) {
            throw new \RuntimeException('No status entity defined.');
        }

        return new $this->statusEntity($status);
    }
}
