<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Hydrator;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Elements implementing this interface provide a hydrator strategy for the containing form.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface HydratorStrategyProviderInterface
{

    /**
     * Set the hydrator strategy for this element.
     *
     * @param StrategyInterface $strategy
     *
     * @return self
     */
    public function setHydratorStrategy(StrategyInterface $strategy);

    /**
     * Get the hydrator strategy for this element.
     *
     * @return StrategyInterface
     */
    public function getHydratorStrategy();
}
