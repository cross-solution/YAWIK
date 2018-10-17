<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Interop\Container\ContainerInterface;
use Jobs\Form\ListFilterLocationFieldset;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for the ListFilterLocation (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ListFilterLocationFieldsetFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $container->get('Geo/Options');
        $fs = new ListFilterLocationFieldset(['location_engine_type' => $options->getPlugin()]);
        return $fs;
    }
}
