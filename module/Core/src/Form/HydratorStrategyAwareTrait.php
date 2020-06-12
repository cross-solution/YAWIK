<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Form;

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\StrategyEnabledInterface;

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
