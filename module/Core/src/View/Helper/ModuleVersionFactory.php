<?php declare(strict_types=1);
/**
 * YAWIK
 *
 * @filesource
 * @copyright 2019 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

namespace Core\View\Helper;

use Interop\Container\ContainerInterface;

/**
 * Factory for \Core\View\Helper\ModuleVersion
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ModuleVersionFactory
{
    public function __invoke(
        ContainerInterface $container,
        ?string $requestedName = null,
        ?array $options = null
    ): ModuleVersion {
        $moduleManager = $container->get('ModuleManager');
        $modules       = array_filter(
            $moduleManager->getLoadedModules(),
            function ($i) {
                return strpos($i, 'Zend\\') !== 0 && strpos($i, 'Doctrine') !== 0;
            },
            ARRAY_FILTER_USE_KEY
        );

        return new ModuleVersion($modules);
    }
}
