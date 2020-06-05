<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Entity\Hydrator\Factory;

use Core\Entity\Hydrator\ImageSetHydrator;
use Core\Options\ImageSetOptions;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \Core\Entity\Hydrator\ImageSetHydrator
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImageSetHydratorFactory implements FactoryInterface
{

    /**
     * Create service.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return ImageSetHydrator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imagine = $container->get('Imagine');
        $options = $container->get($this->getOptionsName());
        $hydrator = new ImageSetHydrator($imagine, $options);

        return $hydrator;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ImageSetHydrator::class);
    }


    /**
     * Get the options key to load the specific options.
     *
     * @return string
     */
    protected function getOptionsName()
    {
        return ImageSetOptions::class;
    }
}
