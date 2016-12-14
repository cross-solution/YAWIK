<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Tree;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * ${CARET}
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface TreeInterface extends EntityInterface, IdentifiableEntityInterface
{
    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param int $priority
     *
     * @return self
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * Has this node any children.
     *
     * @internal
     *      This is needed for the {@link \Core\Form\Hydrator\TreeHydrator}
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Get the children of this node.
     *
     * @return Collection|\Traversable|array
     */
    public function getChildren();

}