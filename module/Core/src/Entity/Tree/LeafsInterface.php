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
use Doctrine\Common\Collections\Collection;

/**
 * Interface for an entity which can manage attached leafs.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface LeafsInterface extends EntityInterface
{

    /**
     * Get the attached leafs.
     *
     * @return Collection
     */
    public function getItems();

    /**
     * Set the attached leafs.
     *
     * @param Collection $items
     *
     * @return mixed
     */
    public function setItems(Collection $items);
}
