<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Service;

use Core\Options\ImagineOptions;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for Imagine service.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImagineFactory implements FactoryInterface
{
    /**
     *
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return \Imagine\Image\ImagineInterface
     * @throws \UnexpectedValueException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get(ImagineOptions::class);
        $lib     = $options->getImageLib();

        switch ($lib) {
            default:
                throw new \UnexpectedValueException('Unsupported image library specified.');
                break;

            case ImagineOptions::LIB_GD:
            case ImagineOptions::LIB_IMAGICK:
            case ImagineOptions::LIB_GMAGICK:
                $imagineClass = '\Imagine\\' . $lib . '\Imagine';
                $imagine = new $imagineClass;
                break;
        }

        return $imagine;
    }
}
