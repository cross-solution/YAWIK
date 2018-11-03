<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\StrategyEnabledInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait HydratorStrategyAwareTrait
{
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->injectHydratorStrategies($hydrator);

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return parent::setHydrator($hydrator);
    }

    protected function injectHydratorStrategies(HydratorInterface $hydrator)
    {
        if ($hydrator instanceof StrategyEnabledInterface) {
            foreach ($this as $name => $elementOrFieldset) {
                if ($elementOrFieldset instanceof HydratorStrategyProviderInterface
                    && !$hydrator->hasStrategy($name)
                ) {
                    $hydrator->addStrategy($name, $elementOrFieldset->getHydratorStrategy());
                }
            }
        }
    }
}
