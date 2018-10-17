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

use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Attachable entity boilerplate for attached leafs.
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
abstract class AttachedLeafs extends AbstractLeafs implements IdentifiableEntityInterface
{
    use IdentifiableEntityTrait;
}
