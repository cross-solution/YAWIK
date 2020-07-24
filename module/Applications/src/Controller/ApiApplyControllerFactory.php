<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright 2020 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

declare(strict_types=1);

namespace Applications\Controller;

use Applications\Entity\Hydrator\ApiApplicationHydrator;
use Interop\Container\ContainerInterface;

/**
 * Factory for \Applications\Controller\ApiApplyController
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ApiApplyControllerFactory
{
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        ?array $options = null
    ): ApiApplyController {
        $repositories = $container->get('repositories');
        return new ApiApplyController(
            $repositories->get('Applications'),
            $repositories->get('Jobs'),
            $container->get('forms')->get('Applications/Apply'),
            $container->get('HydratorManager')->get(ApiApplicationHydrator::class)
        );
    }
}
