<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    Mathias Weitz <weitz@cross-solution.de>
 */


/** Core Entitys */
namespace Core\Entity;

use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\HydratorAwareInterface;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * every derived entity class automatically can be bind to a form and extract or hydrate its values to the form-elements,
 * the hydrator always can be overwritten in the form-class, the hydrator of the entity is just a fall-back,
 * if no other hydrator has been found.
 */
abstract class AbstractIdentifiableHydratorAwareEntity extends AbstractIdentifiableEntity implements HydratorAwareInterface
{
    protected $hydrator;

    /**
     * Set hydrator
     *
     * @param  HydratorInterface $hydrator
     * @return HydratorAwareInterface
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Retrieve hydrator
     *
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (isset($this->hydrator)) {
            return $this->hydrator;
        }
        return new EntityHydrator();
    }
}
