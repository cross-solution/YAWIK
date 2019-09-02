<?php declare(strict_types=1);
/**
 * YAWIK
 *
 * @filesource
 * @copyright 2019 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

namespace Jobs\Controller\Plugin;

use Interop\Container\ContainerInterface;

/**
 * Factory for \Jobs\Controller\Plugin\ProcessJsonRequest
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ProcessJsonRequestFactory
{
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        ?array $options = null
    ): ProcessJsonRequest {
        $viewHelpers = $container->get('ViewHelperManager');
        return new ProcessJsonRequest(
            $viewHelpers->get('serverUrl'),
            $viewHelpers->get('basePath'),
            $viewHelpers->get('dateFormat'),
            $viewHelpers->get('jobUrl'),
            $viewHelpers->get('applyUrl'),
            $container->get('Organizations\ImageFileCache\Manager')
        );
    }
}
