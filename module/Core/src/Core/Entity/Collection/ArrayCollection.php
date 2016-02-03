<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ArrayCollection.php */
namespace Core\Entity\Collection;

class ArrayCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    public function fromArray(array $elements)
    {
        /*
         * This must be an foreach loop and call the inherited method add,
         * because $_elements is declared PRIVATE.
         * Doctrine is build very unextendable! :(
         */
        foreach ($elements as $element) {
            $this->add($element);
        }
        return $this;
    }
}
