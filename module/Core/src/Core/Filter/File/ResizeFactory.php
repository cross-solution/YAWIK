<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Filter\File;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
