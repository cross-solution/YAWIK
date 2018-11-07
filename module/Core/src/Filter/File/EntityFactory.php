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
 * Factory for \Core\Filter\File\Entity
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class EntityFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (isset($options['repository'])) {
            $options['repository'] = true === $options['repository']
                ? $container->get('repositories')
                : (false !== $options['repository'] ? $container->get('repositories')->get($options['repository']) : null);
        }

        $service = new Entity($options);
        
        return $service;
    }
}
