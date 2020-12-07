<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Organizations\Controller;

use Psr\Container\ContainerInterface;

/**
 * Factory for \Organizations\Controller\FeaturedCompaniesController
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class FeaturedCompaniesControllerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): FeaturedCompaniesController {
        return new FeaturedCompaniesController(
            $container->get('repositories')->get('Organizations')
        );
    }
}
