<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright 2020 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

declare(strict_types=1);
namespace Applications\Entity\Hydrator;

use Interop\Container\ContainerInterface;

/**
 * Factory for \Applications\Entity\Hydrator\ApiApplicationHydrator
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ApiApplicationHydratorFactory
{
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        ?array $options = null
    ): ApiApplicationHydrator {
        $hydrator =  new ApiApplicationHydrator();

        $hydrator->setServerURl(
            $container->get('ViewHelperManager')->get('serverurl')->__invoke()
        );

        return $hydrator;
    }
}
