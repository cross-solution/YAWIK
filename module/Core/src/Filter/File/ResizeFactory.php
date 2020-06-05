<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Filter\File;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Filter\File\Resize
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ResizeFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!is_array($options)) {
            $options = [];
        }

        $imagine            = $container->get('Imagine');
        $options['imagine'] = $imagine;
        $service            = new Resize($options);

        return $service;
    }
}
