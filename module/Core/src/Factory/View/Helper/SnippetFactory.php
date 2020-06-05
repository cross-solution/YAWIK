<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Factory\View\Helper;

use Core\View\Helper\Snippet;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for Snippet view helper
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
class SnippetFactory implements FactoryInterface
{

    /**
     * Creates snippet view helper
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return Snippet
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $config = isset($config['view_helper_config']['snippets']) ? $config['view_helper_config']['snippets'] : [];

        $events = $container->get('Core/ViewSnippets/Events');

        $helpers = $container->get('ViewHelperManager');
        $partials = $helpers->get('partial');

        return new Snippet($partials, $events, $config);
    }


    /**
     * Creates snippet view helper-
     *
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     *
     * @return Snippet
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //$container = $serviceLocator->getServiceLocator();

        return $this($serviceLocator, Snippet::class);
    }
}
