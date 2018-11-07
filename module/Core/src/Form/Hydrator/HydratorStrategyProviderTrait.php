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

use Zend\Hydrator\Strategy\DefaultStrategy;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Implementation of the {@link HydratorStrategyProviderInterface}.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait HydratorStrategyProviderTrait
{

    /**
     * The hydrator strategy to provide.
     *
     * @var StrategyInterface
     */
    private $hydratorStrategy;

    public function setHydratorStrategy(StrategyInterface $strategy)
    {
        $this->hydratorStrategy = $strategy;

        return $this;
    }

    public function getHydratorStrategy()
    {
        if (!$this->hydratorStrategy) {
            $strategy =
                method_exists($this, 'getDefaultHydratorStrategy')
                ? $this->getDefaultHydratorStrategy()
                : new DefaultStrategy();

            $this->setHydratorStrategy($strategy);
        }

        return $this->hydratorStrategy;
    }
}
