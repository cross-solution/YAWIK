<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Factory\Form;

use \Core\Form\Element\Select;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the state select element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class StatusSelectFactory implements FactoryInterface
{
    /**
     *
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return Select
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Applications\Repository\Application $applications */
        $repositories = $container->get('repositories');
        $applications = $repositories->get('Applications');
        $states       = $applications->getStates();
        array_unshift($states, 'all');
        $valueOptions = array_combine($states, $states);
        $select       = new Select();

        $select->setValueOptions($valueOptions);

        return $select;
    }

    /**
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     *
     * @return Select
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Select::class);
    }
}
